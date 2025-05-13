$(function () {
    var Script = {};
    (function (app) {
        var ww = window.innerWidth;
  
        app.init = function() {
            app.updateTime();
            app.stopWatchCount();
        }
  
        app.updateTime = function(){
            setInterval(function(){
              var currentDate = $('#current-date').val();
              var currentTime = $('#current-time').val();
          
              var datetimeString = currentDate + ' ' + currentTime;
              var datetime = moment(datetimeString, 'MMMM DD, YYYY HH:mm:ss A');
              datetime = datetime.add(1, 'seconds');
              var formattedTime = datetime.format('HH:mm:ss A');
              $('#current-time').val(formattedTime);
              $('.dtr-stop-watch-count').text(formattedTime);
          
            }, 1000);
        }

        app.stopWatchCount = function(){

          setInterval(function() {
            var timeIn = $('.dtr-time-btn').val();
  
            if(timeIn != 0){
              var currentTime = moment();
              var timeInArray = timeIn.split(':');
              var targetTime = moment().set({ hour: timeInArray[0], minute: timeInArray[1], second: 0, millisecond: 0 });
              var timeDifference = currentTime.diff(targetTime);
              var duration = moment.duration(timeDifference);
              var hours = Math.floor(duration.asHours());
              var minutes = duration.minutes();
              var seconds = duration.seconds();
  
              hours < 10 ? hours = '0' + hours : hours;
              minutes < 10 ? minutes = '0' + minutes : minutes;
              seconds < 10 ? seconds = '0' + seconds : seconds;
            
              $('.dtr-stop-watch').text(hours + ':' + minutes + ':' + seconds);
            }
          }, 1000);
        }
  
        app.init();
    })(Script);
  });