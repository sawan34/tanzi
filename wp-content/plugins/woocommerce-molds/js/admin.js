// LAUNCH 
function selectProduct() {
	var list = document.getElementById("products").value;
}

function overlay($id) {
	var el_id = "products" + $id;
	var el = document.getElementById(el_id);
	el.style.display = (el.style.display == "block") ? "none" : "block";
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
	el.style.height = (el.style.height == "100%") ? "0" : "100%";
	el.style.padding = (el.style.padding == "10px") ? "0" : "10px";
	el.style.marginTop = (el.style.marginTop == "5px") ? "0" : "5px";
}

// GET PRODUCT ID AND RETURN IT TO PAGE
function getProductID($value) {
	display_field = document.getElementById("selected_product");
	display_field.innerHTML = $value;
}

// SUBMIT PRODUCT POST
function submitProduct()
{
  document.post_product.submit();
}

function show_it(id,display=false,getvalue=false,element="") {
	el = document.getElementById(id);
	if(getvalue) {
		elinput = document.getElementById(element);
		if(elinput.checked) {
			el.style.visibility = "visible";
			if(display==true) {
				el.style.display = "inline-block";
			}
		} else {
			el.style.visibility = "hidden";
			if(display==true) {
				el.style.display = "none";
			}
		}
	} else {
		el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
		if(display==true) {
			el.style.display = (el.style.display == "inline-block") ? "none" : "inline-block";
		}
	}
}

function post_product($id,$name) {
	div = document.getElementById($id);
	div.innerHTML += '<input type="hidden" name="product_name" value="'+$name+'">';
	div.innerHTML += '<input type="hidden" name="product" value="'+$id+'">';
	document.forms["moldForm"].submit();
}

function base_price(sale=false) {

	div_id = 'is_base_price';
	add_id = 'is_add';
	sub_id = 'is_sub';
	percent_id = 'is_percent';
	
	if(sale==true) {
		div_id = 'sale_price_is_base_price';
		add_id = 'sale_price_is_add';
		sub_id = 'sale_price_is_sub';
		percent_id = 'sale_price_is_percent';
	}

	div = document.getElementById(div_id);
		add = document.getElementById(add_id);
		sub = document.getElementById(sub_id);
		percent = document.getElementById(percent_id);
	if(div.checked) {
		add.disabled = true;
		add.checked = false;
		sub.disabled = true;
		sub.checked = false;
		percent.disabled = true;
		percent.checked = false;
	} else {
		add.disabled = false;
		sub.disabled = false;
		percent.disabled = false;
	}
}

function add_or_sub(add_click=true,sale=false) {
	
	add_id = 'is_add';
	sub_id = 'is_sub';
	div_id = 'is_base_price';
	
	if(sale==true) {
		add_id = 'sale_price_is_add';
		sub_id = 'sale_price_is_sub';
		div_id = 'sale_price_is_base_price';
	}
	
	div = document.getElementById(div_id);
	add = document.getElementById(add_id);
	sub = document.getElementById(sub_id);
	if(!div.checked) {
		if(add_click) {
			if(add.checked) {
				sub.checked = false;
			}
		} else {
			if(sub.checked) {
				add.checked = false;
			}
		}
	}
}

function all_or_else(terms) {
	divall = document.getElementById('all');
	var tarray = terms.split(" ");
	if(divall.checked) {
		for(k=0;k<tarray.length;k++) {
			term = document.getElementById(tarray[k]);
			term.checked = false;
			term.disabled = true;
		}
	} else {
		for(k=0;k<tarray.length;k++) {
			term = document.getElementById(tarray[k]);
			term.disabled = false;
		}
	}
}

function disabled_all(id) {
	divthis = document.getElementById(id);
	divall = document.getElementById('all');
	if(divthis.checked) {
		divall.checked=false;
		divall.disabled=true;
	} else {
		divall.disabled=false;
	}
}

function uncheck_this(id,other_id) {
	divthis = document.getElementById(id);
	divother = document.getElementById(other_id);
	if(divthis.checked) {
		divother.checked=false;
		divother.disabled=true;
	} else {
		divother.disabled=false;
	}
}

function save_new() {
	document.getElementById('action').value='home';
	document.forms["moldForm"].submit();
}

function delete_image() {
	alert("youpi");
}