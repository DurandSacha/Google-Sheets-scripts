function scrapEmail(url) {
  if(url === '' || url === undefined){
    return 'Pas de site Web visible';
  }
  else if(UrlFetchApp.fetch(url).getResponseCode() !== 200){
    return 'Erreur 500';
  }
  else{
    //console.log(url);
    var response = UrlFetchApp.fetch(url);
    if(response.getResponseCode() !== 200 ){
      return response.getResponseCode();
    }
    var textHTML = response.getContentText();

    if(textHTML.length > 5000){
      var iteration = Math.ceil((textHTML.length/5000)+ 1);
    }
    else{
      var htmlPart = textHTML.substr(0, 5000);
      var mail0 = htmlPart.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z._-]+)/);

      if(mail0 != null){
        //console.log(mail0);
        return mail0;
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
        var mail = htmlPart.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z._-]+)/);
        if(mail != null ){
          if (Array.isArray(mail) == true){
            console.log(mail[0]);
            return mail[0]
          }
          else{
            console.log(mail);
            return mail;
          }
        }

      }
    }
  }
}
