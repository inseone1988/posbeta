var sheetsChart;

function sheetChart(mData){
    $("#csCounter").text(mData[0].ammount);
    $("#osCounter").text(mData[1].ammount);
    $("#dcsCounter").text(mData[2].ammount);
    var ctx = document.getElementById("sheetCounter");
    var options = {
        type : "bar",
        data : {
            labels : ["Papel"],
            datasets : [{
                label: "Carta",
                data: [mData[0].ammount],
                backgroundColor : [
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor : [
                    'rgba(255,99,132,1)'
                ],
                borderWidth : 1
            },{
                label: "Oficio",
                data: [mData[1].ammount],
                backgroundColor : [
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor : [
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth : 1
            },{
                label: "Doble carta",
                data: [mData[2].ammount],
                backgroundColor : [
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor : [
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth : 1
            }
            ]
        },
        options : {
            preserveAspectRatio : false,
            scales : {
                yAxes : [{
                    ticks : {
                        beginAtZero : true
                    }
                }]
            }
        }
    };
    sheetsChart = new Chart(ctx,options);
}