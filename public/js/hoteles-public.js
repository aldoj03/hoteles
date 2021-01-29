jQuery(document).ready(function( $ ){
    let typingTimer
    let site = document.getElementById("site_input")
    let doneTypingInterval = 500

    site.addEventListener('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => doneTyping(site.value), doneTypingInterval);
      });
    
    
    site.addEventListener('keydown', function () {
        clearTimeout(typingTimer);
    });



    function doneTyping(site){
        $.ajax({
            url : 'https://api.mapbox.com/geocoding/v5/mapbox.places/'+site.value+'.json'+'?access_token=pk.eyJ1IjoiZGFuaWVsc3NmIiwiYSI6ImNra2lsa2hmZjA5aXYyb252NzlrOWU4dnUifQ.CN5bJfpaXyT-M8GToUfXTQ',
            data :{
            
            },
            type : 'GET',
            dataType : 'json',
            success : function(json) {
                console.log(json)
            },
            error : function(xhr, status) {
                console.log('Disculpe, existió un problema');
            },
        });
    }
});

    
