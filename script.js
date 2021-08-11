function scrapEmailWithAPI(url) {
  // Call the Numbers API for random math fact
  var response = UrlFetchApp.fetch(url,
    { 'muteHttpExceptions': true, 'validateHttpsCertificates': false}
  );
  return response.getContentText();
}

// scrap an email from url string
function scrapEmail(url) {
  if(url === '' || url === undefined){
    return 'Pas de site Web visible';
  }
  else{
    //console.log(url);
    var response = UrlFetchApp.fetch(url);
    var textHTML = response.getContentText();

    if(textHTML.length > 5000){
      var iteration = Math.ceil((textHTML.length/5000)+ 1);
    }
    else{
      var htmlPart = textHTML.substr(0, 4999);
      var mail0 = htmlPart.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[/fr|com|org|net|us|info/]+)/);

      if(mail0 != null){
        //console.log(mail0);
        return mail0[0];
      }
      var iteration = -1;
    }
    if(iteration > 0){
      for (let i = 1; i < iteration + 1 ; i++){
        var htmlPart = textHTML.substr(i * 5000, i * 5000 + 5000);
        htmlPart = htmlPart.replace('>', ' ');
        htmlPart = htmlPart.replace('<', ' ');
        htmlPart = htmlPart.replace('<br/>', '');
        htmlPart = htmlPart.replace('nbsp', '');
        var mail = htmlPart.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[/fr|com|org|net|us|info/]+)/);
        if(mail != null ){
          if (Array.isArray(mail) == true){
            console.log(mail[0]);
            return mail[0]
          }
        }

      }
    }
  }
}

// scrap an phone number from url string
function scrapPhone(url) {
  if(url === '' || url === undefined){
    return 'Pas de site Web visible';
  }
  else{
    //console.log(url);
    var response = UrlFetchApp.fetch(url);
    var textHTML = response.getContentText();

    if(textHTML.length > 5000){
      var iteration = Math.ceil((textHTML.length/5000)+ 1);
    }
    else{
      var htmlPart = textHTML.substr(0, 4999);
      var mail0 = htmlPart.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z._-]+)/);

      if(mail0 != null){
        //console.log(mail0);
        return mail0[0];
      }
      var iteration = -1;
    }
    if(iteration > 0){
      for (let i = 1; i < iteration + 1 ; i++){
        var htmlPart = textHTML.substr(i * 5000, i * 5000 + 5000);
        htmlPart = htmlPart.replace('>', ' ');
        htmlPart = htmlPart.replace('<', ' ');
        htmlPart = htmlPart.replace('<br/>', '');
        htmlPart = htmlPart.replace('nbsp', '');
        var mail = htmlPart.match(/((\+)33|0|0033)[1-9](\d{2}){4}/igm);
        if(mail != null ){
          if (Array.isArray(mail) == true){
            console.log(mail[0]);
            return mail[0]
          }
        }

      }
    }
  }

}
