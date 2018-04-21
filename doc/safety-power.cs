using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Data.OleDb;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Net.Mail;
using System.Text;
using System.Threading.Tasks;

// notes:
// This program is not running (not decided how we want to use the API information)
// It is currently set to send an alert to support about the urea day tank levels of each generator
// Data seems to be about 10 minutes delayed when the generator is running
// Feel free to delete my first attempt code (see comments), I kept it there for reference

// I downloaded curl.exe for windows to test the API like the website's linux command line example
// but httpWebRequest works similarily as seen in the function "getResponse"

// Payload seems to be the 'tag' (like html) name of every JSON response file from the API...
// Problem because 2 classes can't have same name

namespace testGenAPI
{
    // for the url: "https://safetypower.net/api/1.0/stations/2496771/tags"
    // where 2496771 is the generator ID (Rich Hill)

    public class Laststate
    {
        public string value { get; set; }
        public string timestamp { get; set; }
    }

    public class Payload // tags
    {
        public string name { get; set; }
        public string description { get; set; }

        public Laststate laststate { get; set; }
    }

    public class StationTags // Root object for tags
    {
       /* public StationTags() // test temporary solution to naming class other than Payload
        * (it compiled but it was a null list)
        {
            this.tags = new List<Tag>();
        }*/

        public List<Payload> payload { get; set; }
    }

    /* //FIRST ATTEMPT CODE
    // for the url: "https://safetypower.net/api/1.0/stations" (all stations, general information)

    public class Payload // stations
    {
        public int id { get; set; }
        public string name { get; set; }
        public string sn { get; set; }
        public string lastcomm { get; set; }
    }

    public class Stations // Root object for general station information
    {
        public List<Payload> payload { get; set; }
    } */

    class Program
    {
        static void Main(string[] args)
        {
            List<string> support = new List<string>();

            support.Add("support@greatcirclesolar.ca");
            support.Add("dmacabales@greatcirclesolar.ca");
            support.Add("jastejada@greatcirclesolar.ca");

            int numGenerators = 12;

            //manipulate this url to get more detailed information per site;
            //see examples at https://safetypower.net/?q=api/explorer
            string url = "https://safetypower.net/api/1.0/stations/";

            // password so keep safe; needed for every request
            string accessToken = "QJbs0soFrj3IukQQNyIAvTi0l7iLNQAtAL";

            string root = AppDomain.CurrentDomain.BaseDirectory;

            root = root.Substring(0, root.IndexOf("\\testGenAPI"));

            /* //FIRST ATTEMPT CODE
            // attempt to get simple station information to later build more complicated urls

            string respStr = getResponse(url, accessToken); //JSON response string

            Debug.Print("Response : " + respStr); // can also use Console.WriteLine

            // Parse JSON to class object //downloaded - Json.NET (Newtonsoft.Json NuGet package)
            //make sure root object matches url format!
            Stations st = JsonConvert.DeserializeObject<Stations>(respStr);

            int[] ids = getIDs(st); //test*/

            /*// Example alert (similar to pingCheck)
            string message = getLastCommStatus(st);

            if (message != "")
            {
                SendEmail("Lost Comms", support, message, false, "");
            }*/

            // 2nd attempt - Got tag data and made simple alert

            //hardcoded
            int[] ids = { 2496771, 2497284, 2497797, 2550636, 2551149, 2551662,
                2552175, 2552688, 2553201, 2553714, 2554227, 2554740 };
            string[] names = { "Richmond Hill #1028", "Markham #1032", "Oakville #1024",
                "Winston Churchill #1080", "Milton #2810", "Newmarket #1018", "Glen Erin #1011",
                "South Orleans Ottawa #1071", "Oshawa #1043", "Whitby #1058",
                "Highland Hills #1000", "Aurora #1030" };

            // Mode of operation tag information
            //harcoded tag name (can change to any tag and make email alert message)
            string tagName = "ML060_o";
            string tagsSuffix = @"/tags";
            string result = "";

            StationTags[] stationInfo = new StationTags[numGenerators];

            string[] opsModeValues = new string[numGenerators];
            double latest = 0;
            DateTime dt;

            //17 = numTags + 2 (for site name and timestamp)
            string[,] dataToPrint = new string[numGenerators, 17];

            int tagNum;
            string message = "";

            for (int i=0; i< numGenerators; i++)
            {
                result = getResponse(url + Convert.ToString(ids[i]) + tagsSuffix, accessToken); //JSON strinf

                Debug.Print(result);

                stationInfo[i] = JsonConvert.DeserializeObject<StationTags>(result);

                dataToPrint[i, 0] = names[i]; // name

                tagNum = 2; //reset

                //can only change name of the root object -> otherwise has to match the JSON names
                foreach (Payload t in stationInfo[i].payload)
                {
                    // Put tag data in multi array and later print to the database
                    dataToPrint[i,tagNum] = t.laststate.value;

                    // check for email alert
                    if (t.name == tagName)
                    {
                        opsModeValues[i] = t.laststate.value;
                        latest = Convert.ToDouble(t.laststate.timestamp);

                        // urea test
                        message += names[i] + " urea day tank level is: " + opsModeValues[i]
                            + "(timestamp: "
                            + UnixTimeToDateTime(latest).ToString("MMMM d, yyyy h:mm:ss tt") + ")";
                    }
                    tagNum++;
                }

                dt = UnixTimeToDateTime(latest);

                dataToPrint[i, 1] = dt.ToString("MMMM d, yyyy h:mm:ss tt");// time for database
            }

            dt = UnixTimeToDateTime(latest);

            // Send email alert
            //string message = makeOpsModeMessage(opsModeValues, names);

            if (message!= "")
            {
                //string data = "As of <b>" + dt.ToString("MMMM d, yyyy h:mm:ss tt")
                //+ "</b>: <br>" + message;  //for ops mode message
                SendEmail("Urea day tank level Status", support, message, false, ""); //change as needed
            }

            // Put data into database and later on the webpage? ...
            //intoDatabase(dataToPrint, root);
        }

        // IMPORTANT
        // Method equivalent to the curl GET command, returns JSON string

        public static string getResponse(string url, string accessToken)
        {
            string respStr = "";

            var httpWebRequest = (HttpWebRequest)WebRequest.Create(url);

            httpWebRequest.ContentType = "application/json";
            httpWebRequest.Accept = "*/*";
            httpWebRequest.Method = "GET";
            httpWebRequest.Headers.Add("Authorization", String.Format("Bearer {0}", accessToken));

            var httpResponse = (HttpWebResponse)httpWebRequest.GetResponse(); //add try/catch later
            respStr = new StreamReader(httpResponse.GetResponseStream()).ReadToEnd();

            return respStr;
        }

        public static DateTime UnixTimeToDateTime(double unixTimeStamp)
        {
            // Unix timestamp is seconds past epoch
            System.DateTime dtDateTime = new DateTime(1970, 1, 1, 0, 0, 0, 0, System.DateTimeKind.Utc);
            dtDateTime = dtDateTime.AddSeconds(unixTimeStamp).ToLocalTime(); //EST
            return dtDateTime;
        }

        private static string makeOpsModeMessage(string[] opsModeValues, string[] names)
        {
            string message = "";
            string run = "";
            string shut = "";
            string start = "";
            string err = "";

            for (int i = 0; i < opsModeValues.GetLength(0); i++)
            {
                if (opsModeValues[i] == "3")
                {
                    shut += "<br>" + names[i] + " is shutting down.";
                }
                else if (opsModeValues[i] == "2")
                {
                    run += "<br>" + names[i] + " is running.";
                }
                else if (opsModeValues[i] == "1")
                {
                    start += "<br>" + names[i] + " is starting up.";
                }
                else if (opsModeValues[i] == "0")
                {
                    //standby -> ALL GOOD (no email)
                }
                else
                {
                    err += "<br> Error: invalid Mode of Operation state value for station " + names[i];
                }
            }
            message = start + shut + run + err;

            return message;
        }

        // Unfinished function to put data in a database (ex. Webpage program) continut to build this
        public static void intoDatabase(string[,] weatherForecast, string root)
        {
            //into Database
            OleDbConnection con = new OleDbConnection(@"Provider=Microsoft.Jet.OLEDB.4.0;Data Source="
                    + root + @"\WebpageDatabase.mdb"); //test!!
            OleDbCommand cmd = con.CreateCommand();

            con.Open();

            //for loop for 12 entries...
            cmd.CommandText = "Insert into GenAPIData([Site],[Timestamp],[SS120_o]" //add other tags
                + ")Values('" + weatherForecast[1, 0] + "','" + weatherForecast[1, 1]
                + "','" + weatherForecast[1, 2] + "')";

            cmd.Connection = con;
            cmd.ExecuteNonQuery();

            //end for loop
            con.Close();
        }

        public static void SendEmail(string subject,
                List<string> recipients, string body, bool addAttachment, string excelFilePath)
        {
            MailMessage email = new MailMessage();
            SmtpClient client = new SmtpClient();

            email.From = new MailAddress("alerts@greatcirclesolar.ca");

            foreach (string recipient in recipients)
            {
                email.To.Add(recipient);
            }

            email.Subject = subject;
            email.Body = body;
            email.IsBodyHtml = true;

            if (addAttachment)
            {
                Attachment att = new Attachment(excelFilePath);
                email.Attachments.Add(att);
            }

            client.UseDefaultCredentials = false;
            client.Credentials = new NetworkCredential("alerts@greatcirclesolar.ca", "Password3!");
            client.Port = 587;
            client.Host = "smtp.outlook.com";
            client.EnableSsl = true;
            client.Send(email);
        }

        /*//FIRST ATTEMPT CODE
        private static int[] getIDs(Stations st)
        {
            int[] ids = new int[12]; //hardcoded for the 12 known generators
            int i = 0;

            foreach (Payload p in st.payload)
            {
               ids[i] = p.id;
               i++;
               Debug.Print(p.name);
            }
            return ids;
        }

        public static string getLastCommStatus(Stations st)
        {
            string message = "";

            //check status & send email

            foreach (Payload p in st.payload)
            {
                if (p.lastcomm != "1")
                {
                    message += Convert.ToString(p.name) + "has lost communication. <br>";
                }
            }
            return message;
        }*/
    }
}

/* API's Available Tags for each generator: (values might be 0 or 1 as in True/False or
 * actual numerical values)

* {
    "payload": [
        {
            "name": "SS120_o",
            "description": "Mode of operation  0=Standby, 1=Startup, 2=Run, 3=Shutdown",
            "laststate": {
                "value": "0",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "SS120_o_runtime",
            "description": "Total SCR injection time in seconds",
            "laststate": {
                "value": "754764",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "SS120_o_fault",
            "description": "SCR system fault",
            "laststate": {
                "value": "0",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "SS120_o_alarm",
            "description": "SCR system alarm",
            "laststate": {
                "value": "0",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "EL040_o",
            "description": "Engine Load in KW",
            "laststate": {
                "value": "0",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "UF210_o_lph",
            "description": "Total Flow Rate for all pumps in L\/H",
            "laststate": {
                "value": "0",
                "timestamp": "1512675363"
            }
        },
        {
            "name": "UP210_o",
            "description": "Urea Pump speed command in mV 0-5V signal",
            "laststate": {
                "value": "0",
                "timestamp": "1512675363"
            }
        },
        {
            "name": "NX010_o_NOx",
            "description": "Outlet NOx in ppm",
            "laststate": {
                "value": "1",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "NX010_o_O2",
            "description": "Outlet O2 in %",
            "laststate": {
                "value": "20",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "TC215_o",
            "description": "SCR Catalyst Temperature in deg C",
            "laststate": {
                "value": "6",
                "timestamp": "1512675363"
            }
        },
        {
            "name": "PR030_o_dp",
            "description": "DPF Differential Pressure",
            "laststate": {
                "value": "0",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "ML060_o",
            "description": "Urea day tank level",
            "laststate": {
                "value": "39",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "NX020_o_NOx",
            "description": "Inlet NOx in ppm",
            "laststate": {
                "value": "1",
                "timestamp": "1512675362"
            }
        },
        {
            "name": "XX111_o_cputemp",
            "description": "Controller cpu temperature",
            "laststate": {
                "value": "37",
                "timestamp": "1512675363"
            }
        },
        {
            "name": "XX111_o_system_boottime",
            "description": "Controller boottime as a unixtime",
            "laststate": {
                "value": "1508430000",
                "timestamp": "1512675363"
            }
        }
    ]
}
*/
