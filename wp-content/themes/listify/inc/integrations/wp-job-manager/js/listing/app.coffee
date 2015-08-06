class ListifySingleMap
  constructor: () ->
    @canvas = 'listing-contact-map'

    if ! document.getElementById( @canvas ) then return;
  
    @setOptions()
    @setupMap()
    @setMarker()
  
  setOptions: =>
    @options = listifySingleMap;
 
    @latlng = new google.maps.LatLng @options.lat, @options.lng
    @zoom = parseInt @options.mapOptions.zoom
    @styles = @options.mapOptions.styles
  
    @mapOptions = {
      zoom: @zoom
      center: @latlng
      scrollwheel: false
      draggable: false,
      styles: @styles 
    }
  
  setupMap: =>
    @map = new google.maps.Map document.getElementById( @canvas ), @mapOptions
  
  setMarker: =>
    @marker = new RichMarker(
      position: @latlng
      flat: true
      draggable: false
      content: '<div class="map-marker type-' + @options.term + '"><i class="' + @options.icon + '"></i></div>'
    ) 

    @marker.setMap @map
  
initialize = () ->
  new ListifySingleMap()
  
google.maps.event.addDomListener window, 'load', initialize

jQuery ($) ->
  class ListifyListingComments
    constructor: ->
      @bindActions()

    bindActions: =>
      $( '.comment-sorting-filter' ).on 'change', (e) ->
        $(@).closest( 'form' ).submit()

      $( '#respond .stars-rating .star' ).on 'click', (e) =>
        e.preventDefault()

        @toggleStars(e.target)

    toggleStars: (el) =>
      $( '#respond .stars-rating .star' ).removeClass 'active'

      el = $(el);
      el.addClass 'active'

      rating = el.data 'rating'

      if $( '#comment_rating' ).length == 0
        $( '.form-submit' ).append $( '<input />' ).attr({ type: 'hidden', id: 'comment_rating', name: 'comment_rating', value: rating })
      else
        $( '#comment_rating' ).val rating

  new ListifyListingComments()

jQuery ($) ->
  class ListifyListingGallery
    constructor: ->
      @slick()
      @gallery()

    gallery: =>
      $( '.listing-gallery__item-trigger' ).magnificPopup
        type: 'ajax'
        ajax:
          settings:
            type: 'GET'
            data: { 'view': 'singular' }
        gallery:
          enabled: true
          preload: [1,1]
        callbacks:
          open: ->
            $( 'body' ).addClass( 'gallery-overlay' );
          close: ->
            $( 'body' ).removeClass( 'gallery-overlay' );
          lazyLoad: (item) ->
            $thumb = $( item.el ).data( 'src' );
          parseAjax: (mfpResponse) ->
            mfpResponse.data = $(mfpResponse.data).find( '#main' );
	
    slick: =>
	  $( '.listing-gallery' ).slick
        slidesToShow: 1
        slidesToScroll: 1
        arrows: false
        fade: true
        adaptiveHeight: true
        asNavFor: '.listing-gallery-nav'

      $('.listing-gallery-nav').slick
        slidesToShow: 7
        slidesToScroll: 4
        asNavFor: '.listing-gallery'
        dots: true
        arrows: false
        focusOnSelect: true
        infininte: true
        responsive: [
          {
            breakpoint: 1200,
            settings: {
              slidesToShow: 5,
            }
          }
        ]

  new ListifyListingGallery()
