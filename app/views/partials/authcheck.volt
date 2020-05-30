<div class="container">
  <div id="password-modal" class="w3-modal">
    <div class="w3-modal-content w3-card-8" style="max-width:450px;margin-top:150px">
      <header class="w3-container w3-teal">
        <span class="w3-button w3-teal w3-display-topright" onclick="closeModal()">&times;</span>
        <div class="w3-padding-top w3-padding-bottom"><b>Authorization</b></div>
      </header>

      <p style="text-align: center;">
        <img src="/assets/app/img/gcs-logo-name-223x38.png">
      </p>

      <form class="w3-container" onsubmit="checkAuth(); return false;">
        <div class="w3-section">

          <div class="w3-row-padding">
            <div class="w3-third w3-padding-8">
              <label><b>Password</b></label>
            </div>
            <div class="w3-twothird">
              <input type="password" id="password"
                class="w3-input w3-border w3-margin-bottom"
                placeholder="Enter Your Password"
                autocomplete="off"
                autofocus>
            </div>
          </div>

          <button class="w3-btn-block w3-teal w3-section w3-padding" type="button" onclick="checkAuth()">Verify Password</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="toast" class="error" style="display:none;"></div>

<script>
  //var authorized = false;

  function openModal() {
    $('#password').val('');
    document.getElementById('password-modal').style.display='block';
  }

  function closeModal() {
    $('#password').val('');
    document.getElementById('password-modal').style.display='none';
  }
/*
  function authCheck() {
    var password = $('#password').val();
    $.post('/tangent/checkauth', { password }, function(res) {
      if (res.status != 'OK') {
        $('#toast').text("Wrong Password").fadeIn(400).delay(3000).fadeOut(400);
      } else {
        authorized = true;
      }
    });
    closeModal();
  }
*/
</script>
