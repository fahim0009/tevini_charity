
    function collaps(){
      let p = document.getElementById("sidebar");
          p.classList.toggle("active");
    }

//   var mycls = document.getElementsByClassName('nav-link');
//   for(let i=0; i<=mycls.length; i++){
//      mycls[i].addEventListener("click",function(){
//       for (var j = 0; j < mycls.length; j++) {
//         mycls[j].classList.remove('active');
//       }
//       this.classList.add('active');
//     });
//   }


  function decrementQty() {
    let quantity = document.getElementById("quantity");
    quantity = parseFloat(quantity.value--);
    quantity = quantity - 1;
  }

  function increamntQty() {
    let quantity = document.getElementById("quantity");
    quantity = parseFloat(quantity.value++);
    quantity = quantity + 1;

    let product_price = document.getElementById("product_price");
    product_price = parseFloat(product_price.value);
    let price = quantity * product_price;

    let product_total_price = document.getElementById("product_total_price");
    product_total_price.innerHTML = price;
  }



  function dec(id,amt) {
    let val = document.getElementById('cartValue'+id).value--;
    let aval = document.getElementById('cartValue'+id).value;
    $("#amt"+id).html("<div class='items'>£"+amt*aval+"</div>");
    if (val <= 1) {
        document.getElementById('dec'+id).disabled = true;
    }
  }


  function inc(id,amt) {
    let val = document.getElementById('cartValue'+id).value++;
    let aval = document.getElementById('cartValue'+id).value;
    $("#amt"+id).html("<div class='items'>£"+amt*aval+"</div>");
    if (val >= 1) {
        document.getElementById('dec'+id).disabled = false;
    }
  }
