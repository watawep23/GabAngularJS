var app = angular.module('myApp', ['ngRoute', 'ngAnimate', 'ngFileUpload']);

//for route
app.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
    $routeProvider
    .when("/angular", {
        templateUrl : "/angular/view/create.html"
    })
    .when("/angular/retrieve", {
        templateUrl : "/angular/view/retrieve.html"
    })
    .when("/angular/update", {
        templateUrl : "/angular/view/update.html"
    })
    .when("/angular/delete", {
        templateUrl : "/angular/view/delete.html"
    });

    $locationProvider.html5Mode(true);
}]);

//for banner and navigators
app.controller('headCtrl', function($scope){
	$scope.title = "gabriel adawag";
	$scope.subtitle = "web developer";
	$scope.navs = [
		{name: 'create', link: '/angular'},
		{name: 'retrieve', link: '/angular/retrieve'},
		{name: 'update', link: '/angular/update'},
		{name: 'delete', link: '/angular/delete'}
	];
});

//for person form
app.controller('personCtrl', ['$scope', '$http', 'Upload', '$timeout', function($scope, $http, Upload, $timeout){
    $scope.person = {};

    //im using upload file library so i used Upload dependency.
    $scope.submitPerson = function(file) {
        file.upload = Upload.upload({
            url: "angular/php/add_person.php",
            file: file,
            data: $scope.person,
        }).then(
            function onSuccess(response){
                $timeout(function () {
                    //get the data from PHP file. this will be object becuase of header of PHP file.
                    $scope.status = response.data;

                    //clear input fields and set form to default validation if the insert is successful.
                    if($scope.status.digit == 1){
                        $scope.person = {};
                        $scope.personForm.pFirstName.$touched = false;
                        $scope.personForm.pLastName.$touched = false;
                        $scope.personForm.pEmail.$touched = false;
                        $scope.photoFile = null;
                    }
                }, 1000);
                
            }, 
            function onError(response){
                $scope.status = response.statusText;
            }, 
            function (evt) {
                // Math.min is to fix IE which reports 200% sometimes
                file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            }
        );
    }
}]);

app.controller('retCtrl', ['$scope', '$http', function($scope, $http){
    $http({
        method: "GET",
        url: "angular/php/retrieve_person.php"
    }).then(
        function onSuccess(response){
            $scope.names = response.data.records;
        },
        function onError(response){
            $scope.error = response.statusText;
        }
    );

    $scope.orderBy = function(orderBy){
        $scope.myOrderBy = orderBy;
    }
}]);

//for update record directive
app.directive('updateRecord', function(){
    return {
        restrict: "A",
        link: function(scope, element, attrs){
            element.bind('click', function(){
                $('.people-photo').toggleClass("hide");
            });
        }
    }
});