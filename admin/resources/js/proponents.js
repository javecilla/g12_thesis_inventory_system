jQuery(document).ready(function() {
  //fetch the JSON data
  $.getJSON('resources/json/proponents.json', function(data) {
    //get the container where to add the content
    var container = $('#proponents-container');
    var isPair = true; //set to true initially

    //loop all the in json file 
    $.each(data.proponents, function(pairIndex, pair) {
      //create a new carousel item for each pair of data elements
      var proponentHTML = `
        <div class="carousel-item ${isPair ? 'active' : ''}">
          <div class="row">
        `;
        //loop through the researcher pair in each slide
        $.each(pair, function(memberIndex, proponent) {
          proponentHTML += `
            <div class="col-md-6">
              <div class="carousel-caption">
                <div class="row">
                  <div class="col-sm-3 col-4 align-items-start">
                    <img src="${proponent.imagesrc}" width="100" class="img-fluid" loading="lazy"/>
                  </div>
                  <div class="col-sm-9 col-8">
                    <h2>${proponent.name} - <span>${proponent.position}</span></h2>
                    <small>${proponent.description}</small>
                    <small class="smallest mute"></small>
                  </div>
                </div>
              </div>
            </div>
          `;
        });
      proponentHTML += `
        </div>
         </div>
      `;

      //append the generated HTML to the container
      container.append(proponentHTML);

      //after the first pair, subsequent pairs should have the 'active' class
      isPair = false;
    });
  });
});