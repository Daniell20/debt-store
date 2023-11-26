var dataValues = [userCreditBalance, userCreditLeft, userDebts, totalCustomerInterest]; // Actual data values
var customColors = ["#5D87FF", "#ecf2ff", "#FF5733", "#13DEB9"];
// ApexCharts configuration
var creditLimit = {
	chart: {
		width: 148,
		type: "donut",
		fontFamily: "Plus Jakarta Sans, sans-serif",
		foreColor: "#adb0bb",
	},
	plotOptions: {
		pie: {
			startAngle: 0,
			endAngle: 360,
			donut: {
				size: '75%',
			},
		},
	},
	stroke: {
		show: false,
	},
	dataLabels: {
		enabled: false,
	},
	legend: {
		show: false,
	},
	colors: customColors, // Adjust colors as needed
	responsive: [
		{
			breakpoint: 991,
			options: {
				chart: {
					width: 150,
				},
			},
		},
	],
	tooltip: {
		theme: "dark",
		fillSeriesColor: false,
	},
};

// Create the ApexCharts instance
var userCreditChart = new ApexCharts(document.querySelector("#userCredit"), {
	...creditLimit,
	labels: ["Credit", "Balance", "Debts", "Total Interest"], // Labels
	series: dataValues, // Data values
});
// Render the chart
userCreditChart.render();

var debtGraph = {
	chart: {
		id: "sparkline3",
		type: "area",
		height: 60,
		sparkline: {
			enabled: true,
		},
		group: "sparklines",
		fontFamily: "Plus Jakarta Sans', sans-serif",
		foreColor: "#adb0bb",
	},
	series: [
		{
			name: "Loans",
			color: "#FF866A",
			data: $.map(userLoanAmount, function (value) {
				return [value.amount];
			}),
		},
	],
	stroke: {
		curve: "smooth",
		width: 2,
	},
	fill: {
		colors: ["#f3feff"],
		type: "solid",
		opacity: 0.05,
	},

	markers: {
		size: 0,
	},
	tooltip: {
		theme: "dark",
		fixed: {
			enabled: true,
			position: "right",
		},
		x: {
			show: false,
		},
	},
};
new ApexCharts(document.querySelector("#debtGraph"), debtGraph).render();

