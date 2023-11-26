<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Under Construction</title>

	<style>
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
			overflow: hidden;
		}

		.under-construction-container {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 100%;
			height: 100%;
			position: relative;
			background-color: #333; /* Background color for the container */
			color: #fff; /* Text color */
			font-family: Arial, sans-serif;
		}

		.under-construction-text {
			text-align: center;
		}

		.under-construction-text h1 {
			font-size: 36px;
			margin-bottom: 20px;
			font-weight: bold;
			text-transform: uppercase;
			animation: pulse 2s infinite; /* Example animation */
		}

		.under-construction-text p {
			font-size: 18px;
		}

		@keyframes pulse {
			0%, 100% {
				transform: scale(1);
			}
			50% {
				transform: scale(1.05);
			}
		}
	</style>
</head>
<body>
    <div class="under-construction-container">
        <div class="under-construction-text">
            <h1>Are you lost?</h1>
            <p>We are working on something awesome! Please check back later.</p>
        </div>
    </div>
</body>
</html>