<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Teste Frontend</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-grid.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-reboot.css">
		<link rel="stylesheet" type="text/css" href="css/font-awesome.css">
		<script src="js/jquery-3.2.1.js"></script>
		<script src="js/knockout-3.4.2.js"></script>
	</head>
	<body>
		<div class="container" id="page-content">
			<div class="jumbotron">
				<div data-bind="if: finishedLoading() == true">
					<h1 class="display-3">Página de teste API</h1>
					<p class="lead">Este é um front-end de teste das funcionalidades da API Oauth2 do Laravel.</p>
					<hr class="my-4">
					<p>Abaixo você confere algumas funções disponíveis:</p>
					<p class="lead" data-bind="if: !accessToken()">
						<a class="btn btn-primary btn-lg" role="button" href="/redirect">Fazer login</a>
					</p>
					<p class="lead" data-bind="if: accessToken()">
						<a class="btn btn-primary btn-lg" role="button" href="/logout" data-bind="click: logout">Fazer logout</a>
					</p>
					<p data-bind="if: userData()">Logado como: <span data-bind="text: userData().name"></span></p>
				</div>
				<div data-bind="if: finishedLoading() == false">
				
				</div>
			</div>
		</div>
	</body>
</html>
<script>
	function ViewModel() {
		var self = this;
		self.finishedLoading = ko.observable(false);
		self.data = ko.observable({!!isset($response) ? $response : ''!!});
		self.accessToken = ko.computed(function () {
			if (localStorage.hasOwnProperty('access_token')) {
				return localStorage.getItem('access_token');
			} else if (self.data() && self.data().access_token) {
				return self.data().access_token;
			}
		});
		if (self.data() && self.data().access_token) {
			localStorage.setItem('access_token', self.data().access_token);
		}
		
		self.getUserData = function () {
			$.get('/getUser', {"access_token": self.accessToken()}, function (data) {
				self.userData(data);
			}).fail(function() {
				self.logout();
			}).always(function() {
				self.finishedLoading(true);
			});
		};
		self.getUserData();
		self.userData = ko.observable();
		
		self.logout = function () {
			$.get('/logout', {"access_token": self.accessToken()}).always(function() {
				localStorage.removeItem('access_token');
				viewModel.data(undefined);
				self.userData(undefined);
			});
		}
	}
	
	var viewModel = new ViewModel();
	ko.applyBindings(viewModel, document.getElementById('page-content'));
</script>