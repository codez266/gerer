'use strict';
(function(){
	var baseUrl = '/project/spons/';
	// Declare app level module which depends on views, and components
	var myApplication = angular.module("spons", [
	  'ngRoute',
	  //'myApp.view1',
	  //'myApp.view2',
	  'myApp.version'
	]);/*.
	config(['$routeProvider', function($routeProvider) {
	  $routeProvider.otherwise({redirectTo: '/view1'});
	}]);*/
	//var ajaxService = angular.module( 'ajaxService', ['$http'] );
	//ajaxService.factory( 'get', )
    fetchData().then(bootstrapApplication);

    function fetchData() {
        var initInjector = angular.injector(["ng"]);
        var $http = initInjector.get("$http");

        return $http.get("/project/spons/init").then(function(response) {
            myApplication.constant('user', response.data.user);
            myApplication.constant('server', {'url': response.data.server});
        }, function(errorResponse) {
            // Handle error case
        });
    }

    function bootstrapApplication() {
        angular.element(document).ready(function() {
            angular.bootstrap(document, ["spons"]);
        });
    }
	myApplication.controller( 'UserCtrl', ['user', '$scope', function(user,$scope){
		$scope.user = user;
	}] );
	myApplication.controller( 'FirmCtrl',['server','$http', function(server,$http){
		var self = this;
		this.index=-1;
		this.init = function () {
			var url = 'firms';
			return $http.get( url ).then( function( response ) {
				self.firms = response.data;
			},function(errorResponse){

			} );
		}
		this.showCompany = function(index) {
			this.index=index;
		}
		this.init();
	} ] );

	myApplication.controller( 'VisitCtrl',['server','$http', function(server,$http){
		function Firm( data ) {
			this.data = data;
		}
		var self = this;
		this.init = function () {
			var url = 'firms';
			return $http.get( url ).then( function( response ) {
				self.firms = response.data;
			},function(errorResponse){

			} );
		}
		this.init();
	} ] );

	myApplication.config(function($routeProvider) {
	$routeProvider

	    // route for the home page
	    .when('/', {
	        templateUrl : 'pages/firm.html',
	        controller  : 'FirmCtrl'
	    })

	    // route for the visits page
	    .when('/visits', {
	        templateUrl : 'pages/visit.html',
	        controller  : 'VisitCtrl'
	    })
	});
})();

