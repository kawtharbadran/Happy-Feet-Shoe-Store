
function show_modal(message){

    //get the modal to set the message inside and open it
    var modal = document.getElementById('message_modal');

    //set the message and HTML of the modal
    modal.innerHTML =  "<!-- Modal content -->" +
                        "<div class='modal-content'>" +
                            "<span class='close'>&times;</span>" +
                            "<p>" + error_message + "</p>" +
                        "</div>";
                        
    //diplay the modal by changing its CSS to be display: block
    modal.style.display = "block";

    // Get the <span> x element at te top that closes the modal
    var close_span = document.getElementsByClassName("close")[0];
     
    // When the user clicks on <span> (x) at the top right of the modal, 
    // close the modal by changing its CSS to be display : none
    close_span.onclick = function() {
        modal.style.display = "none";
    }
}