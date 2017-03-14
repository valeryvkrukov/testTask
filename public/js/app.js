'use strict';

var app = angular.module('app', []);

app.controller('AppCtrl', ['$scope', '$interval', '$sce', 'Feed', function($scope, $interval, $sce, Feed) {
	var interval;
	$scope.feed = {};
	$scope.title;
	$scope.loader = function() {
		Feed.load('twitter', {'screen_name': $scope.screenName}).then(function(resp) {
			$scope.feed = resp.data;
		});
	};
	$scope.loadFeed = function() {
		if ($scope.screenName != '') {
			$scope.title = $sce.trustAsHtml('Latest 25 items from <em class="text-primary">@' + $scope.screenName + '</em> timeline');
			interval = setInterval(function() {
				$scope.$apply(function() {
					$scope.loader();
				});
			}, 3000);
			$scope.loader();
		}
	};
	$scope.sizeOfObject = function(obj) {
		return Object.keys(obj);
	};
}]);