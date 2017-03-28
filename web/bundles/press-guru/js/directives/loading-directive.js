angular.module('pressGuruApp')
  .directive('loading', function () {
    return {
        template: '<div><div ng-show="loading" class="loading-container"></div><div ng-hide="loading" ng-transclude></div></div>',
        restrict: 'A',
        transclude: true,
        replace: true,
        scope: true,
        compile: function compile(element, attrs, transclude) {
            var spinner = new Spinner().spin();
            var loadingContainer = $(element).find(".loading-container")[0];
            $(loadingContainer).html(spinner.el);
        }
    };
  });