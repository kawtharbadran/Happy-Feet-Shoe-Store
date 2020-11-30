
//select the country code from the country drop-down menu 
function select_country(country_code){

    //Get country select element
    var country_select = document.getElementById("country_code_select");
    if(country_select != undefined){
        for (var i = 0; i < country_select.options.length; i++) {
            if (country_select.options[i].value == country_code) {
                country_select.options[i].selected = true;
                return;
            }
        }
    }
}

//select the state code from the state drop-down menu 
function select_state(state_code){

    //Get country select element
    var state_select = document.getElementById("state_code_select");
    if(state_select != undefined){
        for (var i = 0; i < state_select.options.length; i++) {
            if (state_select.options[i].value == state_code) {
                state_select.options[i].selected = true;
                return;
            }
        }
    }
}
