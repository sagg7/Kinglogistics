const msToTime = (duration) => {
  let minutes = Math.floor((duration / (1000 * 60)) % 60),
      hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

  hours = (hours < 10) ? "0" + hours : hours;
  minutes = (minutes < 10) ? "0" + minutes : minutes;
  if (hours > 0)
      return hours + " hours " + minutes + " minutes";
  else
      return minutes + " minutes";

}

function filtersChange(tablecostumer){

  $.ajax({
    url: '/shipper/status/',
    data: {
        'shipper_id' : $('#shipper').val()
    },
    success: (res) => {
        const costumerTbody = tablecostumer.find('tbody');
        
        costumerTbody.empty();
        let totalAVG = null, totalTAR = null, acumAvg = 0, acumTAR = 0, count = 0;
        for(var i=0; i < res.length; i++)
        {
          let color = 'black'; 
        
          if(res[i].percentage < 100){
            color="red";
          }
            costumerTbody.append(    `<tr><td>${res[i].name}</td>` +
         `<td>${msToTime(res[i].avg*60*1000)}</td>` +
         `<td data-toggle="tooltip" data-html="true" title="${res[i].active_drivers}/${res[i].trucks_required ?? "N/A"}" style="color: ${color}">${(res[i].percentage > 0) ? res[i].percentage+"%" : "N/A"}</td>` +
       `</tr>` ); 
         acumAvg += res[i].avg;
         acumTAR += parseInt(res[i].percentage);
         count++;
         }
         totalAVG = (acumAvg/count);
         totalTAR = (acumTAR/count);
  //  console.log($totalAVG, $totalTAR, $count);
          let color = 'black'; 
        
          if(totalTAR < 100){
            color="red";
          }
         costumerTbody.append(
         `<tr><td>Total</td>` +
         `<td>${msToTime(totalAVG*60*1000)}</td>` +
         `<td style="color: ${color}">${totalTAR}%</td>` +
         `</tr>` 
         );
          },
          error: () => {
        throwErrorMsg();
    }
});
}

(() => {

  
      filtersChange($('#costumerTable'));

    const nameFormatter = (params) => {
        if (params.value)
            return params.value.name;
        else
            return '';
    };
    const carrierPhoneFormatter = (params) => {
        if (params.data && params.data.carrier.phone)
            return params.data.carrier.phone;
        else
            return '';
    };
    const trailerNumberFormatter = (params) => {
        if (params.data && params.data.truck && params.data.truck.trailer)
            return params.data.truck.trailer.number;
        else
            return '';
    };

})();
