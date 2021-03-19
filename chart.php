<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<div class="col-sm-2">
<canvas id="myChart"  ></canvas>
</div>
<div class="col-sm-6">
<canvas id="myChart21" width="200" height="200"></canvas>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script type="text/javascript">
	var ctx = document.getElementById("myChart");
var myChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['< 1/2', '1/2 > 2 h', '> 2h'],

    datasets: [{
      label: '# of Tomatoes',

      data: [12, 19, 3],
      backgroundColor: [
        '#7cbe88',
        '#fcaf17',
         '#fc1717'
      ],
      borderColor: [
        '#7cbe88',
        '#fcaf17',
        '#fc1717'
      ],
      borderWidth: 1
    }]
  },
  options: {
   	//cutoutPercentage: 40,
    responsive: false,

  }
});

$(function(){

  //get the bar chart canvas
  var ctx = $("#myChart2");

  //bar chart data
  var data = {
    labels: ["Jan", "Feb", "March", "Apr", "May","Jun",'Jul',"Aug",'Sept','Oct','Nov','Dec'],
    datasets: [
      {
        label: "TeamA Score",
        data: [10, 50, 25, 70, 40,12,56,23,45,56,89,45],
        backgroundColor: [
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360"
           
        ],
        borderColor: [
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360",
          "#48a360"
                   ],
        borderWidth: 1
      },
      {
        label: "TeamB Score",
        data: [5, 50, 24, 50, 60,12,54,58,85,16,49,15],
        backgroundColor: [
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8"
           
        ],
        borderColor: [
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8",
          "#4dc4b8"
          
        ],
        borderWidth: 1
      }
    ]
  };

  //options
  var options = {
    responsive: true,
    title: {
      display: true,
      position: "top",
      text: "Bar Graph",
      fontSize: 18,
      fontColor: "#111"
    },
    legend: {
      display: true,
      position: "bottom",
      labels: {
        fontColor: "#333",
        fontSize: 16
      }
    },
    scales: {
      yAxes: [{
        ticks: {
          min: 0
        }
      }]
    }
  };

  //create Chart class object
  var chart = new Chart(ctx, {
    type: "bar",
    data: data,
    options: options
  });
});
</script>