window.parseIntSafe = function (val) {
  var r = parseInt(val);
  if (isNaN(r)) return 0;
  return r;
}

window.simplifyString = function (str) {
  return str.normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim();
}

window.getAlpineObj = function (obj) {
  if (typeof obj !== 'object') return obj;
  return JSON.parse(JSON.stringify(obj));
}

window.parseBoolean = function (val) {
  return (val === true
    || val === 'true'
    || val === 1
    || val === '1');
}

window.dateFromYmd = function (dateStr) {
  const date = new Date();
  const dateParts = dateStr.split('-');
  date.setFullYear(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
  date.setHours(0, 0, 0, 0);
  return date;
}

window.getToday = function () {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return today;
}

window.dateToYmd = function (date) {
  return date.getFullYear() + '-'
    + ('0' + (date.getMonth() + 1)).slice(-2) + '-'
    + ('0' + date.getDate()).slice(-2);
}

window.diffForHumans = function (date) {
  const today = window.getToday();
  if (date.getFullYear() === today.getFullYear()
      && date.getMonth() === today.getMonth()
      && date.getDate() === today.getDate()
    ) {
      return 'today';
  }
  const diff = (date - today)/1000;
  const absDiff = Math.abs(diff);
  const formatter = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });
  if (absDiff > 31536000) return formatter.format(Math.round(diff / 31536000), 'year');
  if (absDiff > 2628000) return formatter.format(Math.round(diff / 2628000), 'month');
  if (absDiff > 604800) return formatter.format(Math.round(diff / 604800), 'week');
  return formatter.format(Math.floor(diff / 86400), 'day');
}

window.getReadableAxiosError = function (error) {
  console.log(error ? error.toString() : 'getReadableAxiosError — unknown error');
  let message = "Sorry, an error occurred: \n";
  if (!error || !error.response) {
    message += 'Unknown error.';
  } else if (error.response.status) {
    switch (error.response.status) {
      case 400:
        message += 'The server understood the request, but the request content was invalid.';
        break;
      case 401:
        message += 'Unauthorized access.';
        break;
      case 403:
        message += 'Unauthorized action.';
        if (error.response.data.message) message += "\n" + error.response.data.message;
        break;
      case 404:
        message += 'Page not found.';
        break;
      case 422:
        if (error.response.data.errors && typeof error.response.data.errors === 'object' && error.response.data.errors !== null) {
          let errorsObj = error.response.data.errors;
          let errors = [];
          if (!Array.isArray(errorsObj)) {
            for (const errItem in errorsObj) {
              errors.push(errItem);
            }
          }
          errors.forEach(errItem => {
            if (Array.isArray(errItem)) {
              errItem.forEach(errMsg => { if (typeof errMsg === 'string') message += errMsg + "\n"; });
            } else if (typeof errMsg === 'string') {
              message += errItem + "\n";
            }
          });
        } else if (error.response.data.message) {
          message += error.response.data.message;
        } else {
          message += 'Invalid data.';
        }
      break;
      case 500:
      message += 'Internal server error.';
      break;
      case 503:
      message += 'Service unavailable.';
      break;
      default:
      message += 'Unknown error.';
    }
  } else if (error.request) {
    // The request was made but no response was received
    // `error.request` is an instance of XMLHttpRequest in the browser
    if (error.request.readyState == 4) {
      message += error.request.statusText;
    } else if (error.request.readyState == 0) {
      message += 'You may not be connected to the internet.';
    } else {
      message += 'Unknown error.';
    }
  } else if (error.message) {
    message += error.message;
  } else {
    message += 'Unknown error.';
  }
  return message;
}