function filtersChange(tableCustomer){

    $.ajax({
        url: '/shipper/status/',
        data: {
            'shipper_id' : $('#shipper').val()
        },
        success: (res) => {
            const customerTbody = tableCustomer.find('tbody');

            customerTbody.empty();
            let totalAVG = 0, totalTAR = 0, acumAvg = 0, acumTAR = 0, count = 0;

            for(var i in res.shipperAvg)
            {
                let shipper = res.shipperAvg[i];
                let color = 'black';
                
                if(shipper.percentage < 100){
                    color="red";
                }
                if (shipper.avg > 0 && shipper.loadTime)
                    customerTbody.append(    `<tr><td>${shipper.name}</td>` +
                        `<td>${msToTime(shipper.avg*60*1000, false)}</td>` +
                        `<td>${msToTime(shipper.loadTime*60*1000, false)}</td>` +
                        `<td data-toggle="tooltip" data-html="true" title="${shipper.active_drivers}/${shipper.trucks_required ?? "N/A"}" style="color: ${color}">${(shipper.percentage > 0) ? shipper.percentage+"%" : "N/A"}</td>` +
                        `</tr>` );
                acumAvg += shipper.avg;
                acumTAR += parseInt(shipper.percentage);
                count++;
            }
            if(count !== 0){
                totalAVG = (acumAvg/count);
                totalTAR =  Math.round(acumTAR/count);
            }
            let color = 'black';

            if(totalTAR < 100){
                color="red";
            }
            customerTbody.append(
                `<tr style="border-top:3px solid #d9d9d9"><td>Total</td>` +
                `<td>${msToTime(totalAVG*60*1000, false)}</td>` +
                `<td>${msToTime(res.LoadTimeAvg*60*1000, false)}</td>` +
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
