'use strict';

var app = angular.module('app');

app.factory('Feed', ['$http', '$q', function($http, $q) {
	var baseUrl = '/feed/';
	
	return {
		load: function(resource, params) {
			var d = $q.defer();
			$http({
				url: baseUrl + resource,
				method: 'POST',
				data: params
			}).then(function(data) {
				d.resolve(data);
			});
			return d.promise;
		}
	};
}]);