function filtersChange(tablecustomer){

  $.ajax({
    url: '/shipper/status/',
    data: {
        'shipper_id' : $('#shipper').val()
    },
    success: (res) => {
        const customerTbody = tablecustomer.find('tbody');

        customerTbody.empty();
        let totalAVG = 0, totalTAR = 0, acumAvg = 0, acumTAR = 0, count = 0;
        for(var i=0; i < res.length; i++)
        {
          let color = 'black';

          if(res[i].percentage < 100){
            color="red";
          }
            customerTbody.append(    `<tr><td>${res[i].name}</td>` +
         `<td>${msToTime(res[i].avg*60*1000)}</td>` +
         `<td data-toggle="tooltip" data-html="true" title="${res[i].active_drivers}/${res[i].trucks_required ?? "N/A"}" style="color: ${color}">${(res[i].percentage > 0) ? res[i].percentage+"%" : "N/A"}</td>` +
       `</tr>` );
         acumAvg += res[i].avg;
         acumTAR += parseInt(res[i].percentage);
         count++;
         }
         if(count != 0){
          totalAVG = (acumAvg/count);
          totalTAR =  Math.round(acumTAR/count);}
  //  console.log($totalAVG, $totalTAR, $count);
          let color = 'black';

          if(totalTAR < 100){
            color="red";
          }
         customerTbody.append(
         `<tr style="border:1px solid #d9d9d9"><td>Total</td>` +
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


      filtersChange($('#customerTable'));

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
