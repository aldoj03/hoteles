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