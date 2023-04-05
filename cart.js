changelist();


function addtocart(pid) {
    savetolocal(pid);
    changelist();
}
function savetolocal(pid) {
    var saved_pid = JSON.parse(localStorage.saved_pid || null) || {};
    var saved_quan = JSON.parse(localStorage.saved_quan || null) || {};

    if (saved_pid[0]==null){
        saved_pid[0] = pid;
        saved_quan[0] = 1;
        
    }else { 
        var length = Object.keys(saved_pid).length;
        for(var i=0;i<length;i++){
            if(saved_pid[i]==pid){
                saved_quan[i]++;
                break;
            }
            if (i==length-1){
                saved_pid[length]=pid;
                saved_quan[length]=1;
            }
        }
    }
    localStorage.saved_pid = JSON.stringify(saved_pid);
    localStorage.saved_quan = JSON.stringify(saved_quan);
    
}

function changelist(){
    
    var saved_pid = JSON.parse(localStorage.saved_pid || null) || {};
    var saved_quan = JSON.parse(localStorage.saved_quan || null) || {};
    var totalprice=0;

    var item=document.getElementsByClassName("shopping_list_item")[0];
    for(var i=0;item!=undefined;i++){
        item.remove();
        item=document.getElementsByClassName("shopping_list_item")[0];
    }

    
    var length = Object.keys(saved_pid).length;
    for(var i=0;i<length;i++){
        let li = document.createElement('li');
        li.classList.add('shopping_list_item');
        let text = document.createTextNode('');
        li.appendChild(text);
        document.getElementsByClassName("item_list")[0].appendChild(li);


    
        let li1 = document.createElement('li');
        li1.classList.add('shopping_list_name');
        let text1 = document.createTextNode(saved_pid[i]);
        li1.appendChild(text1);
        document.getElementsByClassName("shopping_list_item")[i].appendChild(li1);
    
        let input = document.createElement('input');
        input.classList.add('item_quantity');
        input=document.getElementsByClassName("shopping_list_item")[i].appendChild(input);
        input.value=saved_quan[i];
        input.onchange = update;
        input.pattern = "[0-9]";

        var request = new XMLHttpRequest();
        var response, n, p;
        request.open("GET", "ajax.php?pid="+saved_pid[i]);
        request.onreadystatechange = function() {
            if(this.readyState === 4 && this.status === 200) {
    
                response= JSON.parse(this.responseText);
                pname=response.name;
                price=response.price;
                pid=response.pid;
                var index=0;
                for(var j=0;j<length;j++){                
                    if(saved_pid[j]==pid){
                        index=j;
                    }
                }

                

                totalprice += price*saved_quan[index];
                //console.log(index+"\n"+totalprice+"\n");
                document.getElementsByClassName("shopping_list_name")[index].innerHTML=pname;
                document.getElementsByClassName("show_shopping_list")[0].innerHTML="Shopping List $"+totalprice;
                
            }
        };
        request.send();
    }
    
}



function update(){
    var q=this.value;
    if (isNaN(q)){
        changelist();
    } else { 
        var saved_pid = JSON.parse(localStorage.saved_pid || null) || {};
        var saved_quan = JSON.parse(localStorage.saved_quan || null) || {};
        var length = Object.keys(saved_pid).length;
        var item=document.getElementsByClassName("item_quantity")[0];
        var i=0;
        var number=0;
        for(i=0;i<length;i++){
            item=document.getElementsByClassName("item_quantity")[i];     
            if (item.value==q) number=i;
        }
        if(q<=0){
            for(i=number;i<length;i++){
                saved_quan[i]=saved_quan[i+1];
                saved_pid[i]=saved_pid[i+1];
                document.getElementsByClassName("show_shopping_list")[0].innerHTML="Shopping List $"+"0";
            }
        } else saved_quan[number]=q;
    
        localStorage.clear();
        localStorage.saved_pid = JSON.stringify(saved_pid);
        localStorage.saved_quan = JSON.stringify(saved_quan);
        changelist();
    }
    

}

function Return_Order_Detail() {
  var saved_pid = JSON.parse(localStorage.saved_pid || null) || {};
  var saved_quan = JSON.parse(localStorage.saved_quan || null) || {};

  return {saved_pid: saved_pid, saved_quan: saved_quan};


}



function getFromServer() {
  var data=Return_Order_Detail();
  return new Promise(resolve => {
    $.ajax({
      data:  data,
      type: "post",
      url: "checkout.php",
      success: function(result){
        result=JSON.parse(result);
        
        let info=result["info"];
        let items=result["items"];
        let items_num=items.length;

        let currency=info["currency"];
        let total_price=info["total_price"];
        let digest=info["digest"];
        let lastInsertId=info["lastInsertId"];
        
        let all_items=[];
        for (i=0;i<items_num;i++){
          item=items[i];
          item_name=item["name"];
          item_name=i+":"+item_name;
          item_value=item["price"];
          item_quan=item["quan"];
          var item_detail = { name: String(item_name), unit_amount: { currency_code: String(currency), value: Number(item_value) }, quantity: Number(item_quan) };
          all_items.push(item_detail);
        }
        
        localStorage.clear();
        document.getElementsByClassName("show_shopping_list")[0].innerHTML="Shopping List $"+"0";
        changelist();
        
        resolve(JSON.stringify({
          purchase_units: [{
            amount: { currency_code: currency, value: total_price, breakdown: { item_total: { currency_code: currency, value: total_price } } },
            custom_id: digest,  /* digest */
            invoice_id: lastInsertId, /* lastInsertId() */
            items: all_items
          }]
        }));
      },
      error: function(result) {
        console.log("error");
      },

    });
  });
}



paypal.Buttons({
  /* Sets up the transaction when a payment button is clicked */
  createOrder: async (data, actions) => { /* async is required to use await in a function */
    /* Use AJAX to get required data from the server; For dev/demo purposes: */

    let order_details = await getFromServer()
      .then(data => JSON.parse(data));
    
    /* Use fetch() instead in real code to get server resources */
    // let order_details = await fetch(/* resource url*/)
    //     .then(response => response.json()) /* json string to javascript object */
    //     .then(data => {
    //         /* process over data */
    //         return /* return value */;
    //     });

    return actions.order.create(order_details);
  },

  /* Finalize the transaction after payer approval */
  onApprove: (data, actions) => {
    return actions.order.capture().then(function (orderData) {
      /* Successful capture! For dev/demo purposes: */
      /*console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
      const transaction = orderData.purchase_units[0].payments.captures[0];
      alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);*/

      /* When ready to go live, remove the alert and show a success message within this page. For example: */
      // const element = document.getElementById('paypal-button-container');
      // element.innerHTML = '<h3>Thank you for your payment!</h3>';
      /* Or go to another URL:  */
      actions.redirect('https://secure.s49.ierg4210.ie.cuhk.edu.hk/');
    });
  },
}).render('#paypal-button-container');


