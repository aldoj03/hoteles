
window.onload = ()=>{

    //set query information in local storage after submit form hotels

    if(document.querySelector('#submit_hotels_form')){
        document.querySelector('#submit_hotels_form')
        .addEventListener('click',()=>{
         const checkIn = document.querySelector('#searchHotels #checkIn').value
         const checkOut = document.querySelector('#searchHotels #checkOut').value
         const hab = document.querySelector('#searchHotels #habitaciones_select').value
         const adultos = document.querySelector('#searchHotels #adultos_select').value
         const ninos = document.querySelector('#searchHotels #ninos_select').value

         const data = {
             checkIn,
             checkOut,
             hab,
             adultos,
             ninos,
             lat: 39.57119,
             lng: 2.646633999999949
         }
         window.localStorage.setItem('checkInData',JSON.stringify(data))

        })
    }

}

jQuery(document).ready(function( $ ){
    let typingTimer
    let site = document.getElementById("site_input")
    let doneTypingInterval = 1000

    site.addEventListener('keyup', function (tecla) {

            let select = document.getElementById("select_input")
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => doneTyping(site.value), doneTypingInterval);
   
      });
    
    
    site.addEventListener('keydown', function () {
        clearTimeout(typingTimer);
    });



    function doneTyping(site){
        let select = document.getElementById("select_input")
        let options = select.options.length;
        for (i = options-1; i >= 0; i--) {
           select.options[i] = null;
        }

        $.ajax({
            url : 'https://api.mapbox.com/geocoding/v5/mapbox.places/'+site+'.json'+'?access_token=pk.eyJ1IjoiZGFuaWVsc3NmIiwiYSI6ImNra2lsa2hmZjA5aXYyb252NzlrOWU4dnUifQ.CN5bJfpaXyT-M8GToUfXTQ',
            data :{},
            type : 'GET',
            dataType : 'json',
            success : function(json) {
                
              
                let length = json["features"].length
                for(let i = 0; i < length ; i++){
                    console.log("sitio: "+json["features"][i]["place_name"])
                    let x = document.createElement("OPTION")
                    x.value = json["features"][i]["center"][0] +"/"+ json["features"][i]["center"][1]
                    x.innerHTML= json["features"][i]["place_name"]
                    select.appendChild(x)
                }
               
            },
            error : function(xhr, status) {
                console.log('Disculpe, existió un problema');
            },
        });
    }
});


