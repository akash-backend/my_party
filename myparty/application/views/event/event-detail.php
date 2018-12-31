
<style type="text/css">
.iii img {
    width: 100%; max-height:300px;
 }
</style>
<div class="forms">
    <h2 class="title1"><?= $title; ?></h2>

    <div class="alert alert-dismissible fade" role="alert"><span id="msg"></span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

    <div class="row">
        <div class="form-three widget-shadow">                                  
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 class="event-detail-hidng">Basic Detail</h3><br>
                </div>
                
                <div class="col-md-12">                    
                   
                    <table class="table table-width">
                          <tr>
                            <th>Event Name</th>
                            <td><?= $event_detail['event_name']; ?></td>
                          </tr>
                          <tr>
                            <th>Venue</th>
                            <td><?= $event_detail['event_venue']; ?></td>
                          </tr>
                          <tr>
                            <th>Event Description</th>
                            <td><?= $event_detail['event_description']; ?></td>
                          </tr>
                          <tr>
                            <th>Start Date</th>
                            <td><?= $event_detail['start_date']; ?></td>
                          </tr>

                          <tr>
                            <th>Accept Count</th>
                            <td> <?= $event_detail['accept_event_count']; ?></td>
                          </tr>
                          <tr>
                            <th>Reject Count</th>
                            <td><?= $event_detail['reject_event_count']; ?></td>
                          </tr>
                          <tr>
                            <th>Event Start Date</th>
                            <td><?= date("h:i a", strtotime($event_detail['event_start_date'])); ?></td>
                          </tr>
                          <tr>
                            <th>Event End Date</th>
                            <td><?= date("h:i a", strtotime($event_detail['event_end_date'])); ?></td>
                          </tr>
                        </tbody>
                      </table>

                </div>

                    <div class="col-sm-12">
                    <div class="form-group row">
                        <label class="control-label">Map</label>
                        <div class="pac-card" id="pac-card">
                            <div>
                                <div id="type-selector" class="pac-controls">
                                </div>
                            </div>
                            <div id="pac-container">
                                <input id="pac-input" type="text"
                                    placeholder="Enter a location" class="form-control">
                            </div>
                        </div>
                        <div id="map" style="height: 250px"></div>
                        <div id="infowindow-content">
                          <span id="place-name"  class="title"></span><br>
                          <span id="place-address"><?php if(!empty($event_detail['event_venue'])){ echo $event_detail['event_venue']; }else{echo "Woolloomooloo NSW 2011, Australia";} ?></span>
                        </div>
                    </div>
                </div>
         
           <input type="hidden" name="lat" id="lat" value="<?php if(!empty($event_detail['event_latitude'])){echo $event_detail['event_latitude'];}?>">
           <input type="hidden" name="lng" id="lng" value="<?php if(!empty($event_detail['event_longitude'])){echo $event_detail['event_longitude'];}?>">

                    
          
        </div>        
    </div>
</div>




<!-- start map  -->

<script>

function initMap() 
{
    var lat=$('#lat').val();
    var lng=$('#lng').val();
    var mylatlng={lat:parseFloat(lat),lng:parseFloat(lng)};
    var address=$("#event_address").val();
    
    if(lat == "" && lng == "")
    {
        lat = -33.8688;
        lng = 151.2195;
        mylatlng={lat:lat,lng:lng};
    }

    if(address=="")
    {
        address="Woolloomooloo NSW 2011, Australia";
    }
    

    var map = new google.maps.Map(document.getElementById('map'), 
    {
      center: mylatlng,
      zoom: 13
    });

    var card = document.getElementById('pac-card');
    var input = document.getElementById('pac-input');
    var types = document.getElementById('type-selector');
    var strictBounds = document.getElementById('strict-bounds-selector');

    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    var infowindowContent = document.getElementById('infowindow-content');

    //console.log(infowindowContent);
    infowindow.setContent(infowindowContent);

    var marker = new google.maps.Marker({
      position: mylatlng,
      map: map,
      anchorPoint: new google.maps.Point(0, -29)
    });

    infowindow.open(map,marker);
    geocoder = new google.maps.Geocoder();
    google.maps.event.addListener(map, 'click', function(event){
        placeMarker(event.latLng);
    });

    //var marker;
    function placeMarker(location) 
    {
        if(marker)
        { //on vérifie si le marqueur existe
            marker.setPosition(location); //on change sa position
        }
        else
        {
            marker = new google.maps.Marker({ //on créé le marqueur
                position: location, 
                map: map
            });
        }
        
        getAddress(location);
    }

  function getAddress(latLng) 
  {
    geocoder.geocode( {'latLng': latLng},
    
    function(results, status) 
    {
        if(status == google.maps.GeocoderStatus.OK) 
        {
          if(results[0]) 
          {
            document.getElementById("event_address").value = results[0].formatted_address;
            document.getElementById('lat').value=results[0].geometry.location.lat();
            document.getElementById('lng').value=results[0].geometry.location.lng();

            //alert(results[0].geometry.location.lat()+'  '+results[0].geometry.location.lng());
            geocoder.geocode( { 'address': results[0].formatted_address}, function(results, status){
                if (status == google.maps.GeocoderStatus.OK) 
                {
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    $("#lat").val(latitude);
                    $("#lng").val(longitude);
                    // alert(latitude);
                } 
            }); 
           }
           else 
           {
             document.getElementById("event_address").value = "No results";
           }
        }
        else 
        {
          document.getElementById("event_address").value = status;
        }
    });
  }

    //
    


        autocomplete.addListener('place_changed', function() {
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
           
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          var address = '';
          if (place.address_components) {
             
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindowContent.children['place-icon'].src = place.icon;
          place_name=infowindowContent.children['place-name'].textContent = place.name;
          infowindowContent.children['place-address'].textContent = address;
          infowindow.open(map, marker);
          // alert(address);
          //   document.getElementById("place_name").value =address;
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
          var radioButton = document.getElementById(id);
          radioButton.addEventListener('click', function() {
            autocomplete.setTypes(types);

          });
        }

        function showInfo(latlng) {
      //alert('hello');
      geocoder.geocode({
        'latLng': latlng
      }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[1]) {
            alert(results[1].formatted_address);
            // here assign the data to asp lables
            //document.getElementById('<%=addressStandNo.ClientID %>').value = results[1].formatted_address;
          } else {
            alert('No results found');
          }
        } else {
          alert('Geocoder failed due to: ' + status);
        }
      });
    }

        setupClickListener('changetype-all', []);
        setupClickListener('changetype-address', ['address']);
        setupClickListener('changetype-establishment', ['establishment']);
        setupClickListener('changetype-geocode', ['geocode']);
            
        document.getElementById('use-strict-bounds')
            .addEventListener('click', function() {
              console.log('Checkbox clicked! New state=' + this.checked);
              autocomplete.setOptions({strictBounds: this.checked});
            });
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9BK-Y2V_cMxV1bwQM-RbS9-Nvbqa4mnw&libraries=places&callback=initMap"
        async defer></script>

        <!-- end map -->




