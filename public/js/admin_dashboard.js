$(function () {

	var options = {
		series: [users, customersToday, merchantsToday, inactiveMerchants, inactiveCustomers],
		chart: {
			width: 500,
			type: 'pie',
		},
		labels: ['Active Users', 'Registered Customers Today', 'Registered Merchants Today', 'Inactive Merchants', "Inactive Customers"],	
		responsive: [{
			breakpoint: 480,
			options: {
				chart: {
					width: "100%"
				},
				legend: {
					position: 'bottom'
				}
			}
		}]
	};

	var chart = new ApexCharts(document.querySelector("#userChartOverview"), options);
	chart.render();

});

// var customColors = ["#FFA500", "#0075A4", "#33CC33", "#999999"];
// var customLabels = ["Customers", "Merchants", "Active User", "Inactive User"]

// var userOptions = {
// 	chart: {
// 		width: 400,
// 		type: 'donut',
// 		fontFamily: "Plus Jakarta Sans, sans-serif",
// 		forColor: "#adb0bb",
// 	},
// 	plotOptions: {
// 		pie: {
// 			startAngle: 0,
// 			endAngle: 360,
// 			donut: {
// 				size: '20%',
// 			},
// 		},
// 	},
// 	stroke: {
// 		show: false,
// 	},
// 	dataLabels: {
// 		enabled: false,
// 	},
// 	legend: {
// 		show: false,
// 	},
// 	colors: customColors,
// 	responsive: [
// 		{
// 			breakpoint: 991,
// 			options: {
// 				chart: {
// 					width: 150
// 				},
// 			},
// 		},
// 	],
// 	tooltip: {
// 		theme: "dark",
// 		fillSeriesColor: true,
// 	}
// };

// var userChart = new ApexCharts(document.querySelector("#userChart"), {
// 	...userOptions,
// 	labels: customLabels,
// 	series: values,
// });

// userChart.render();