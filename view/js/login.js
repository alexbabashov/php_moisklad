class FormHandler 
{
  constructor() 
  {
    this.btnLogin = document.querySelector(".btn");
    this.divAlert = document.querySelector(".alert");
    this.inputUsername = document.querySelector('input[name="username"]');
    this.inputPassword = document.querySelector('input[name="password"]');

    if (this.btnLogin) {
      this.btnLogin.addEventListener('click', this.handleClick.bind(this));
    }
  }

  handleClick(event) 
  {
    this.divAlert.setAttribute('hidden', 'hidden');
    let url = event.target.getAttribute('data-url');
    let loginData = {
      username: this.inputUsername.value,
      password: this.inputPassword.value
    };
    let options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(loginData)
    };
    fetch(url, options)
      .then(response => 
      {
        if (!response.ok) {
          throw new Error('Network response was not ok.');
        }
        return response.json();
      })
      .then(data => 
      {
        this.divAlert.removeAttribute('hidden');
        if (data.error) {
          this.divAlert.setAttribute('class', 'alert alert-danger');
          this.divAlert.innerHTML = data.error;
        } else {
          this.divAlert.setAttribute('class', 'alert alert-success');
          this.divAlert.innerHTML = 'успех';
          if (data.data.url) 
          {
            window.location.href = data.data.url;
          }
          else
          {
            window.location.href = '/';
          }
        }
      })
      .catch(error => 
      {
        console.error('There was a problem with your fetch operation:', error);
        this.divAlert.setAttribute('class', 'alert alert-danger');
        this.divAlert.removeAttribute('hidden');
        this.divAlert.innerHTML = error.message;
      });
  }; 
}

const formHandler = new FormHandler();