jQuery(document).ready(function ($) {

  let canvas = new fabric.Canvas('c', {
    selection: false
  });
  canvas.setHeight(window.innerHeight);
  canvas.setWidth($('#tremtr_main_metabox').width() - 24);
  canvas.defaultCursor = 'crosshair'

  let canvasObjectId = 0;

  let rectangle = '';
  let isDown = '';
  let originX = '';
  let originY = '';

  let currentElement = '';

  let mouseOverObject = null;

  let justCreated = null;

  let missedTables = [];

  let sortedTablesAfterLoad = [];


canvas.observe('mouse:down', function (o) {

  if (o.target != null) {

    let objId = o.target.id;
    let item = '';

    $.each(canvas.getObjects(), function (i) {

      if (Number(objId) === Number(canvas.item(i).id)) {
        item = i;
        return false;
      }
    });

    canvas.set({
      _activeObject: canvas.item(item)
    });

    canvas.forEachObject(function (o) {
      if (o.get('type') === 'rect') {
        o.setCoords();
        o.lockMovementX = false;
        o.lockMovementY = false;
        o.lockScalingX = false;
        o.lockScalingY = false;
        o.lockUniScaling = false;
        o.lockRotation = true;
        o.hasControls = true;
        o.hoverCursor = 'pointer';
        o.hasBorders = true;
        o.borderColor = 'red';
        o.cornerColor = 'green';
        o.cornerSize = 10;
        o.transparentCorners = false;
        o.hasRotatingPoint = false;
      } else {
        o.setCoords();
        o.lockMovementX = true;
        o.lockMovementY = true;
        o.lockScalingX = true;
        o.lockScalingY = true;
        o.lockUniScaling = true;
        o.lockRotation = true;
        o.hasControls = false;
        o.hoverCursor = 'pointer';
        o.hasBorders = false;
        o.selectable = false;
        o.borderColor = 'red';
        o.cornerColor = 'green';
        o.cornerSize = 10;
        o.transparentCorners = false;
        o.evented = false;
      }
    })

    canvas.renderAll();

    
  } else {

    isDown = true;
    let pointer = canvas.getPointer(o.e);
    origX = pointer.x;
    origY = pointer.y;

    let rectangleId = nextTableNumber()

    rectangle = new fabric.Rect({
      left: origX,
      top: origY,
      rx: 10,
      ry: 10,
      strokeWidth: 1,
      stroke: 'black',
      fill: 'transparent',
      selectable: true,
      id: rectangleId,
      lockMovementX: true,
      lockMovementY: true,
      lockScalingX: true,
      lockScalingY: true,
      lockUniScaling: true,
      lockRotation: true,
      hasControls: false,
      hoverCursor: 'pointer',
      hasBorders: false
    });
    rectangle.toObject = (function(toObject) {
      return function() {
        return fabric.util.object.extend(toObject.call(this), {
          name: this.name,
          persons: this.persons,
          active: this.active
        });
      };
    })(rectangle.toObject);  
    rectangle.name = rectangleId;
    rectangle.persons = 1;
    rectangle.active = false; //for front end

    canvas.add(rectangle);

    mouseOverObject = rectangle;
    justCreated = rectangle;

    canvas.renderAll();
  }
});

canvas.observe('mouse:move', function (o) {

  if (!(isDown)) return;
  let pointer = canvas.getPointer(o.e);

  if (origX > pointer.x) {
    justCreated.set({
      left: Math.abs(pointer.x)
    });
  }
  if (origY > pointer.y) {
    justCreated.set({
      top: Math.abs(pointer.y)
    });
  }

  justCreated.set({
    width: Math.abs(origX - pointer.x)
  });
  justCreated.set({
    height: Math.abs(origY - pointer.y)
  });

  justCreated.setCoords()


  canvas.renderAll();

});

canvas.on('mouse:up', function (o) {

  isDown = false;

  if ((rectangle.width < 30 || rectangle.height < 30) && rectangle === justCreated) {

    $.each(canvas.getObjects(), function (i) {

      if (Number(rectangle.id) === Number(canvas.item(i).id)) {

        addMissedTable(rectangle.id)

        canvas.remove(canvas.item(i));
        canvas.renderAll();

        return false;
      }
    });

  } else if (rectangle === justCreated) {

    let table = new fabric.Text(rectangle.name.toString(), {
      fontFamily: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif',
      fontSize: 25,
      id: justCreated.id,
      top: rectangle.top + 10,
      left: rectangle.left + 10,
      selectable: false,
      lockMovementX: true,
      lockMovementY: true,
      lockScalingX: true,
      lockScalingY: true,
      lockUniScaling: true,
      lockRotation: true,
      hasControls: false,
      evented: false
    });


    canvas.add(table);

    let b = rectangle.persons;
    let html = '<li id="' + rectangle.id + '"> <i class="fa fa-circle" style="color: white;"></i> <span class="table" >' + rectangle.name + '</span>  <input type="number" name="people" min="1" step="1" value="' + b + '"> <button class="trem-remove" ><i class="fa fa-times" aria-hidden="true" style="color: #f00;"></button></li>';

    if ($("#control-items").children('li').length !== 0) {
      if (Number(rectangle.id) < Number($("#control-items").children('li')[0].id)) {
        $("#control-items").prepend(html)
      } else {
        $(html).insertAfter($("#control-items").find('#' + (Number(rectangle.id) - 1)));
      }
    } else {
      $("#control-items").append(html)
    }


    $(".trem-remove").click(function (event) {
      event.preventDefault();

      let objId = $(this).closest('li').attr('id');

      $(this).parent().remove();

      $.each(canvas.getObjects(), function (i) {

        if (Number(objId) === Number(canvas.item(i).id)) {
          canvas.remove(canvas.item(i + 1));
          canvas.remove(canvas.item(i));
          canvas.renderAll();

          addMissedTable(objId)

          return false;
        }
      });
    });

    $('input[name="people"]').change(function () {
      
      let objId = $(this).parent('li').attr('id');
      let newVal = $(this).val();

      $.each(canvas.getObjects(), function (i) {

        if (Number(objId) === Number(canvas.item(i).id) && canvas.item(i).type === 'rect') {
          canvas.item(i).persons = Number(newVal);
          canvas.renderAll();
          return false;
        }
      });

    });


    justCreated = null;
  }
});


  //find next table number
  function nextTableNumber () {

    if (missedTables.length !== 0) {

      let next = missedTables[0]
      missedTables.shift()

      return next

    } else {

      let max = 0

      for (let i = 1; i < canvas.getObjects().length; i = i + 2) {
        let tableNumber = Number(canvas.item(i).text)
        if (max < tableNumber) {
          max = tableNumber
        }
      }

      return max + 1
    }
  }

  //create missedTables array after JSON parse (load)
  function createMissedTable () {

    let tables = [0]
    for (let i = 1; i < canvas.getObjects().length; i = i + 2) {
      tables.push(Number(canvas.item(i).text))
    }

    tables.sort(function (a, b) {
      return a - b
    });

    for (let i = 0; i < canvas.getObjects().length; i++) {
      let diff = Number(tables[i + 1]) - Number(tables[i]) - 1
      if (diff !== 0) {
        for (let j = 1; j <= diff; j++) {
          missedTables.push(tables[i] + j)
        }
      }

    }
  }

  //create sorted array of existed tables after JSON parse (load)
  function createSortedTables() {

    let tables = []
    for (let i = 1; i < canvas.getObjects().length; i = i + 2) {
      tables.push(Number(canvas.item(i).text))
    }

    tables.sort(function (a, b) {
      return a - b
    });

    sortedTablesAfterLoad = tables
  }

  //add next table number in missedTables array
  function addMissedTable(id) {
    let value = Number(id);

    missedTables.splice(_.sortedIndex(missedTables, value), 0, value);
  }


  // make correction
  $('html').keyup(function (e) {
    if (e.keyCode == 46) {
      if ((canvas.getObjects().length != 0) && (canvas.getActiveObject() != null)) {

        let objId = canvas.getActiveObject().id;
        let element = '#' + objId;
        $(element).remove();
        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id)) {
            if ((mouseOverObject != null) && (mouseOverObject.id === canvas.item(i).id)) {
              mouseOverObject = null;
            }

            canvas.remove(canvas.item(i + 1));
            canvas.remove(canvas.item(i));
            canvas.renderAll();

            addMissedTable(objId)

            return false;
          }
        });
      }
    }
  });

  canvas.on('mouse:over', function (e) {

    if (e.target != null) {
      mouseOverObject = e.target;
    }
    canvas.renderAll();
  });

  canvas.on('mouse:out', function (e) {

    mouseOverObject = null
  });


  canvas.on('object:moving', function (e) {
    if (canvas.getActiveObject() != undefined) {

      let id = canvas.getActiveObject().id;

      $.each(canvas.getObjects(), function (i) {

        if (!(canvas.item(i).id === undefined)) {
          if (Number(id) === Number(canvas.item(i).id)) {

            canvas.item(i + 1).top = canvas.getActiveObject().top + 10;
            canvas.item(i + 1).left = canvas.getActiveObject().left + 10;

            canvas.renderAll();

            return false;
          }
        }
      });

      canvas.renderAll();
    }
  });


  canvas.on('object:scaling', function (e) {

    if (canvas.getActiveObject() != undefined) {

      let id = canvas.getActiveObject().id;

      $.each(canvas.getObjects(), function (i) {

        if (!(canvas.item(i).id === undefined)) {
          if (Number(id) === Number(canvas.item(i).id)) {

            canvas.item(i + 1).top = canvas.getActiveObject().top + 10;
            canvas.item(i + 1).left = canvas.getActiveObject().left + 10;

            canvas.renderAll();

            return false;
          }
        }
      });
      canvas.renderAll();
    }
  });

  canvas.on('object:modified', function (e) {

    if (canvas.getActiveObject() != undefined) {


      canvas.getActiveObject().set({
        height: Math.round(canvas.getActiveObject().scaleY * canvas.getActiveObject().height)
      });
      canvas.getActiveObject().set({
        width: Math.round(canvas.getActiveObject().scaleX * canvas.getActiveObject().width)
      });
      canvas.getActiveObject().set({
        scaleX: 1
      });
      canvas.getActiveObject().set({
        scaleY: 1
      });
      canvas.renderAll();
    }

  });




  canvas.on('object:selected', function (e) {
    if (currentElement) {
      currentElement.css("color", "white");
    }
    let id = canvas.getActiveObject().id;
    let li = "#" + id.toString();
    currentElement = $(li).children("i");
    currentElement.css("color", "green");

  });


  canvas.on('selection:cleared', function (e) {
    if (currentElement != "") {
      currentElement.css("color", "white");
    }
  });



  $(function () {
    $.contextMenu({
      selector: '.context-menu-one',
      trigger: 'right',
      hideOnSecondTrigger: true,
      reposition: false,
      callback: function (key, options) {
        if (key === 'delete') {
          $('html').trigger(jQuery.Event('keyup', {
            which: 46,
            keyCode: 46
          }));
        }
      },
      items: {
        "delete": {
          name: "Delete",
          icon: "delete",
          disabled: function () {


            if (mouseOverObject != null) {

              let objId = mouseOverObject.id;
              let item = '';

              $.each(canvas.getObjects(), function (i) {

                if (Number(objId) === Number(canvas.item(i).id)) {
                  item = i;
                  return false;
                }
              });

              canvas.set({
                _activeObject: canvas.item(item)
              });
              canvas.renderAll();
            }

            if (mouseOverObject === null) {
              return true;
            } else {
              return false;
            }
          }
        }
      }
    });
  });


  if (typeof wp != 'undefined' && typeof wp.media != 'undefined') {
    let featuredImage = wp.media.featuredImage.frame();
    featuredImage.on('select', function () {
      let attachment = featuredImage.state().get('selection').first().toJSON();

      let newScale = 1;
      canvas.setWidth($('#tremtr_main_metabox').width() - 24);
      if (attachment.width > canvas.width) {
        newScale = canvas.width / Number(attachment.width);
      } else {
        canvas.setWidth(Number(attachment.width));
      }


      canvas.setBackgroundImage(attachment.url, canvas.renderAll.bind(canvas), {
        opacity: 1,
        stretch: false,
        scaleX: newScale,
        scaleY: newScale
      });

      canvas.setHeight(newScale * Number(attachment.height));
    });
  }

  if ($('#tremtr_content').val() != '') {
    canvas.loadFromJSON($('#tremtr_content').val(), function () {

      let objCounter = 1;
      let counter = 0;

      createMissedTable()
      createSortedTables()

      for (let k = 0; k < sortedTablesAfterLoad.length; k++) {
        $.each(canvas.getObjects(), function (i) {
          if (canvas.item(i).get('type') === 'rect' && canvas.item(i + 1).text == sortedTablesAfterLoad[k]) {

            canvas.item(i).id = canvas.item(i+1).text;
            canvas.item(i + 1).id = canvas.item(i + 1).text;
            canvas.item(i).toObject = (function(toObject) {
              return function() {
                return fabric.util.object.extend(toObject.call(this), {
                  name: this.name,
                  persons: this.persons,
                  active: this.active 
                });
              };
            })(canvas.item(i).toObject);  

            let html = '<li id="' + canvas.item(i).id + '"> <i class="fa fa-circle" style="color: white;"></i> <span class="table" >' + canvas.item(i).name + '</span> <input type="number" name="people" min="1" step="1" value="' + canvas.item(i).persons + '">  <button class="trem-remove"><i class="fa fa-times" aria-hidden="true" style="color: #f00;"></button></li>';
            $("#control-items").append(html)

            $(".trem-remove").click(function (event) {
              event.preventDefault();

              let objId = $(this).parent('li').attr('id');

              $(this).parent().remove();

              $.each(canvas.getObjects(), function (i) {

                if (Number(objId) === Number(canvas.item(i).id)) {
                  canvas.remove(canvas.item(i + 1));
                  canvas.remove(canvas.item(i));
                  canvas.renderAll();

                  addMissedTable(objId)

                  return false;
                }
              });
            });
          }
        })
        
      }


      $('input[name="people"]').change(function () {
        let objId = $(this).parent('li').attr('id');
        let newVal = $(this).val();

        $.each(canvas.getObjects(), function (i) {

          if (Number(objId) === Number(canvas.item(i).id) && canvas.item(i).type === 'rect') {
            canvas.item(i).persons = Number(newVal);
            canvas.renderAll();
            return false;
          }
        });

      });



      let newScale = 1;
      if (canvas.backgroundImage !== null) {
        if (canvas.backgroundImage.width > canvas.width) {
          newScale = canvas.width / Number(canvas.backgroundImage.width);
        } else {
          canvas.setWidth(Number(canvas.backgroundImage.width));
        }

        canvas.setHeight(newScale * Number(canvas.backgroundImage.height));
      } else {

        canvas.setWidth($('#tremtr_main_metabox').width() - 24);

        canvas.setHeight(window.innerHeight);
      }


    });
  }



  $('form#post').submit(function (e) {

    $('#tremtr_content').val(JSON.stringify(canvas));


  });

  //prevent enter
  $("#control-items").bind("keypress", function (e) {
    if (e.keyCode == 13) {
      return false;
    }
  });

});
