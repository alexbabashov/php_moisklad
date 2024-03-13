
class OrderHandler 
{
  constructor() 
  {
    this.divAlert = document.querySelector(".alert");
    this.divAlert.setAttribute('hidden', 'hidden');
    this.updateData();
  }

  updateData()
  {
    const url = '/api/orders';
    const options = {
      method: 'GET',
      headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
      },
      //body: JSON.stringify('')
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
      if (!data || !data.data)
      {
        throw new Error('Нет данных');        
      }
      this.updateTable(data.data);                      
    })
    .catch(error => 
    {
      let errMsg = 'Network response was not ok.';
      if(error.error)
      {
          errMsg = error.error;
      }
      else
      {
        errMsg = error;
      }
      console.error('There was a problem with your fetch operation:', error);
      this.divAlert.setAttribute('class', 'alert alert-danger');
      this.divAlert.removeAttribute('hidden');
      this.divAlert.textContent = errMsg;
    }); 
  }

  updateTable(data)
  {
    console.log(data); 
    let objTable = document.querySelector('.table');
    if (!objTable) {return null;}
    let objBody = objTable.querySelector('tbody');
    if (!objBody) {return null;}

    objTable.removeChild(objBody);
    objBody = document.createElement("tbody");
    data.forEach(item => this.addRowToTable(objBody, item));
    objTable.appendChild(objBody);

  };

  addRowToTable(parent, data)
  {
    let tr = document.createElement("tr");
   
    tr.appendChild( this.calcCellTable(data.num, data.id_link) );
    tr.appendChild( this.calcCellTable(data.time_created, null) );
    tr.appendChild( this.calcCellTable(data.agent, data.agent_link) );
    tr.appendChild( this.calcCellTable(data.organization, null) );
    tr.appendChild( this.calcCellTable(data.sum, null) );

    ////////////// State
    let td = null;
    let div = null;

    td = document.createElement("td");
    div = document.createElement("div");
    div.className = 'd-flex flex-row justify-content-center';
    let divParentState = document.createElement("div");
    divParentState.className = 'btn-group';

    let btnState = document.createElement("button");
    btnState.textContent = data.state.current.name;
    btnState.className = 'btn btn-danger dropdown-toggle';
    btnState.setAttribute("type", "button");
    btnState.setAttribute("data-bs-toggle", "dropdown");
    btnState.setAttribute("aria-expanded", "false");
    btnState.setAttribute("data-href", data.state.current.meta.href);
    btnState.setAttribute("data-id-order", data.id);
    divParentState.appendChild(btnState);

    let ulState = document.createElement("ul");
    ulState.className = 'dropdown-menu';
    divParentState.appendChild(ulState);
    
    let count = data.state.states.length;
    for (let i = 0; i < count; i++) 
    {     
      let li = document.createElement("li");

      let div = document.createElement("div");
      if (data.state.current.meta.href === data.state.states[i].meta.href)
      {
        div.className = 'dropdown-item active';
      }
      else{
        div.className = 'dropdown-item';
      }
      div.style.userSelect = "none";
      div.style.webkitUserSelect = "none"; 
      div.style.cursor = "pointer";
      div.textContent = data.state.states[i].name;
      div.setAttribute("data-id-order", data.id);
      div.setAttribute("data-href", data.state.states[i].meta.href);

      div.addEventListener('click', this.changeState.bind(this));

      li.appendChild(div);
      
      ulState.appendChild(li);
    }

    div.appendChild(divParentState);
    td.appendChild(div);
    tr.appendChild(td);
    /////////////////////

    tr.appendChild( this.calcCellTable(data.time_updated, null) );
    
    parent.appendChild(tr);
  }

  calcCellTable(value, url)
  {
    let td = document.createElement("td");
    let div = document.createElement("div");
    div.className = 'd-flex flex-row justify-content-center';

    if( url )
    {
      let linkEl = null;
      linkEl = document.createElement("a");
      linkEl.textContent = value;      
      linkEl.setAttribute("href", url);
      linkEl.setAttribute("target","_blank");   
      div.appendChild(linkEl); 
    }
    else
    {
      div.textContent = value;
    }
    td.appendChild(div);

    return td
  }

  changeState(event)
  {
    this.divAlert.setAttribute('hidden', 'hidden');

    let href = event.target.getAttribute('data-href');
    let idOrder = event.target.getAttribute('data-id-order');
    
    let parent = event.target.closest('.btn-group');
    let button = parent.querySelector('button');
    let parent_href = button.getAttribute('data-href');

    if (parent_href === href) 
    {      
      return;
    }
  
    let body = {
      id: idOrder,
      state: href,
    };

    let url = '/api/orders/state';
    var options = {
      method: 'PUT',
      headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
      },
      body: JSON.stringify(body)
    };  
    fetch(url, options)
    .then(response =>
    {
      
      if (response.ok)
      {
        button.textContent = event.target.textContent;
        button.setAttribute('data-href', href);
        event.target.setAttribute('data-href', href);
        parent_href = button.getAttribute('data-href');
    
        let dropdown_items = parent.querySelectorAll('.dropdown-item');  
        let count = dropdown_items.length;
        for (let i = 0; i < count; i++) 
        {      
          let tmp_href = dropdown_items[i].getAttribute('data-href');
         
          if (tmp_href === parent_href)
          {
           
            dropdown_items[i].className = 'dropdown-item active';
          }
          else{
            dropdown_items[i].className = 'dropdown-item';
          }   
        }
      }
      else
      {
        let errMsg = 'Network response was not ok.';
        if(response.error)
        {
            errMsg = response.error;
        }
        throw new Error(errMsg);      
      }
    })
    .catch(error => {
        console.error('There was a problem with your fetch operation:', error);

        let errMsg = 'Network response was not ok.';
        if(error.error)
        {
            errMsg = error.error;
        }
        this.divAlert.setAttribute('class', 'alert alert-danger');
        this.divAlert.removeAttribute('hidden');
        this.divAlert.textContent = errMsg
    });
  }
}

const orderHandler = new OrderHandler();