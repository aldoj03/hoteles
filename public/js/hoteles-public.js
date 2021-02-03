jQuery(document).ready(function( $ ){
    let typingTimer
    let site = document.getElementById("site_input")
    let doneTypingInterval = 200

      if(site){
          site.addEventListener('keyup', function (tecla) {         
              clearTimeout(typingTimer);
              typingTimer = setTimeout(() => autocomplete(document.getElementById("site_input"), countries), doneTypingInterval);
          });

          site.addEventListener('keydown', function () {
              clearTimeout(typingTimer);
          });
      }

    function autocomplete(inp, arr) {
        var currentFocus;
        inp.addEventListener("input", function(e) {
            var a, b, i, val = this.value;
            closeAllLists();
            if (!val) { return false;}
      
            $.ajax({
                  url : `https://api.mapbox.com/geocoding/v5/mapbox.places/${val}.json?access_token=pk.eyJ1IjoiZGFuaWVsc3NmIiwiYSI6ImNra2lsa2hmZjA5aXYyb252NzlrOWU4dnUifQ.CN5bJfpaXyT-M8GToUfXTQ`,
                  data :{},
                  type : 'GET',
                  dataType : 'json',
                  success : function(json) {
                    console.log(json["features"][0]["center"])
                    let lat = document.getElementById("lat")
                    lat.setAttribute("value",json["features"][0]["center"][0]+'/'+json["features"][0]["center"][1])
                    console.log(lat)
                    for(let i = 0; i < json["features"].length ; i++){
                        arr[i] = json["features"][i]["place_name"]
                      }
                      currentFocus = -1;
                      a = document.createElement("DIV");
                      a.setAttribute("id", this.id + "autocomplete-list");
                      a.setAttribute("class", "autocomplete-items");
                      inp.parentNode.appendChild(a);
                      for (i = 0; i < arr.length; i++) {
                        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                          b = document.createElement("DIV");
                          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                          b.innerHTML += arr[i].substr(val.length);
                          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                          b.addEventListener("click", function(e) {
                              inp.value = this.getElementsByTagName("input")[0].value;
                              closeAllLists();
                          });
                          a.appendChild(b);
                        }
                      }
                  },
                  error : function(xhr, status) {
                      console.log('Disculpe, existió un problema');
                  },
              });
        });
      
      
        inp.addEventListener("keyup", function(e) {
            var x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
              currentFocus++;
              addActive(x);
            } else if (e.keyCode == 38) {
              currentFocus--;
              addActive(x);
            } else if (e.keyCode == 13) {
              e.preventDefault();
              if (currentFocus > -1) {
                if (x) x[currentFocus].click();
              }
            }
        });
      
      
        function addActive(x) {
          if (!x) return false;
          removeActive(x);
          if (currentFocus >= x.length) currentFocus = 0;
          if (currentFocus < 0) currentFocus = (x.length - 1);
          x[currentFocus].classList.add("autocomplete-active");
        }
      
      
        function removeActive(x) {
          for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
          }
        }
      
      
        function closeAllLists(elmnt) {
          var x = document.getElementsByClassName("autocomplete-items");
          for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
              x[i].parentNode.removeChild(x[i]);
            }
          }
        }
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
        });
      }
      let countries = []
      
      
});


window.onload = ()=>{

  //set query information in local storage after submit form hotels

  if(document.querySelector('#submit_hotels_form')){
      document.querySelector('#submit_hotels_form')
      .addEventListener('click',()=>{
       const checkIn = document.querySelector('#searchHotels #checkIn').value
       const checkOut = document.querySelector('#searchHotels #checkOut').value
       const hab = document.getElementById('habitaciones_select selects').value
       const adultos = document.getElementById('adultos_select selects').value
       const ninos = document.getElementById('ninos_select selects').value
       
       let ubicacion  = document.getElementById("lat").value
       let array = ubicacion.split("/")
       
       const data = {
           checkIn,
           checkOut,
           hab,
           adultos,
           ninos,
           lat: array[0],
           lng: array[1]
       }
       window.localStorage.setItem('checkInData',JSON.stringify(data))

      })
  }

}