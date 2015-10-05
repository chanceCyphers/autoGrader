window.rowCounter=0;

function ajax(url) {
   var obj;
   if (window.XMLHttpRequest) {
      obj=new XMLHttpRequest();
   }
   else {            
       obj=new ActiveXObject("Microsoft.XMLHTTP");
   }
   if (obj) {
      obj.open("GET", url, false);
      obj.send(null);
      return obj.responseText;
   }
   return "";
}

var oldInfo=[], oldIndex='x', newValue='';
alert(index);
function responseFunc(index) {
   oldInfo[oldIndex] = document.getElementById('responseTypeDiv').innerHTML;
   if (oldInfo[index]) {
      newValue = oldInfo[index];
   }
   else {
      switch(index) {
         case 'multipleChoice': newValue=
            "<table id='"+index+"'> \
			<tr id='row0'><td style='text-align:center'><br /><b>Multiple Choice</b><br />\
			   Do you want the choices presented in a random/different sequence each time? \
			      <input type='radio' name='randomOrder'>Yes&nbsp;&nbsp;&nbsp \
			      <input type='radio' name='randomOrder' checked='checked'>No<br /> \
<b>You may now enter the choices. Click insert-here to enter the choices one at a time.</b><br /> \
                  <a href=\"javascript:updateResponses('"+index+"', 'row0','insert')\">insert-here</a> \
			</td></tr>";

            break;

         case 'essay': newValue=
            "<table id='"+index+"'> \
			<tr id='row0'><td style='text-align:center'><br /><b>Essy or Written Words</b><br />\
			   If you want the response to be matched against one or more keys, click insert-here to enter the keys one at a time. Note that a response is deemed correct if it matches <b>any one</b> of the key(s).</b><br /> \
                  <a href=\"javascript:updateResponses('"+index+"', 'row0','insert')\">insert-here</a> \
			</td></tr>";
            break;

           case "program": newValue=
              "<table id='"+index+"'> \
			<tr id='row0'><td style='text-align:center'><br /><b>Computer Program</b><br /> \
The Submitted program may be automatically executed and tested for you. If you want this, click insert-here to enter one or more test input data and expected output (key) below. Note<ul> \
<li>A submission is deemed correct if it yeilds the matching key for every input data. \
<li>One or more white spaces in a row are converted to a single blank character when answer and key are compared e.g. two white spaces in an output are treated the same as one blank character.\
<li>If you upload a file for the test input data or for the output key, the submitted program must read from the same file name or write to the same file name as the one you submitted. \
<li>For now, you can not have multiple input or out medium e.g. a program that reads from both the console and a file.</ul>/<br />\
<a href=\"javascript:updateResponses('"+index+"', 'row0','insert')\">insert-here</a> \
			</td></tr>";
            break;




         default: newValue='abc';
      }
   }
   document.getElementById('responseTypeDiv').innerHTML=newValue;
   oldIndex = index;
}

//<input type='radio' name='cb"+tableId+"[]'>
function updateResponses(tableId, rowId, action) {
   var pos, row, cell, table, i;
   // find the targetted row
   table = document.getElementById(tableId);
   for (i = 0; row = table.rows[i]; i++) {
      if (table.rows[i].id==rowId) break; // found targetted row
   }
   pos = i+1;

   switch (action) {
      case 'insert':
         rowCounter++;
	 row = table.insertRow(pos); 
	 row.id="row"+rowCounter;
	 var cell = row.insertCell(0);
	 cell.style.textAlign = 'center';
         if (tableId=="multipleChoice") {
	    content =
	       "<hr /><span style='vertical-align:150%'>Enter Choice:</span><textArea name='ta"+tableId+"[]' rows='3' cols='50'></textArea><br /> \
	       Check here if this is a correct answer <input type='radio' name='correctResponse[]' value='0' checked='checked'>No&nbsp;&nbsp;&nbsp;<input type='radio' name='correctResponse[]' value='1'>Yes";
         }
         else if (tableId=="essay") {
	    content =
	       "<hr /><span style='vertical-align:150%'>Enter Key:</span><textArea name='ta"+tableId+"[]' rows='3' cols='50'></textArea>";
         }
         else if (tableId=="program") {
            content =
               "<hr /> \
            How would you like to enter the test input data?<br /> \
            <input type='radio' name='input"+rowCounter+"' value='kb' style='margin-left:50px' onClick=\"document.getElementById('d1"+rowCounter+"').style.display='block';document.getElementById('d2"+rowCounter+"').style.display='none';\">Type or paste in the question&nbsp;&nbsp;&nbsp;&nbsp<input type='radio' name='input"+rowCounter+"' value='file' onClick=\"document.getElementById('d1"+rowCounter+"').style.display='none';document.getElementById('d2"+rowCounter+"').style.display='block';\">Upload a file with the question<br /> \
            <div id='d1"+rowCounter+"' style='display:none'> \
               <span style='font-weight:bold;text-align:center'>Enter the question below</span> \
               <textArea rows=10 cols=80 name='kb"+rowCounter+"'></textArea> \
            </div> \
            <div id='d2"+rowCounter+"' style='display:none'><span style='font-weight:bold;text-align:center'>Select file to upload</span><input type='file' name='f"+rowCounter+"' size='30'>\
</div>";

         }

         cell.innerHTML = content+
   "<br /><a href=\"javascript:updateResponses('"+tableId+"', 'row"+rowCounter+"','insert')\">insert-here</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:updateResponses('"+tableId+"', 'row"+rowCounter+"','delete')\">delete</a>";


         break;

      case "delete":
         if (confirm("Are you sure you want to delete row?")) {
            table.deleteRow(pos-1);
            alert("Deleted");
         }
         else alert("Delete aborted!");
         break;

   }
}	  


function nextFindMenu(t) {
   name = t.name;
   value = t.options[t.selectedIndex].value;
   id = t.id+"";
   n = id.substr(1)-0;   // get the number part of id
   // wipeout all rows below n
   tid = document.getElementById("findTable");
   for (i=n+1; i < tid.rows.length; i++) {
      tid.rows[i].style.display="none";
   }
   if (t.selectedIndex == 0) return; // return if 1st entry "Select" is selected
   // make the next row after selection visible with the proper entries in the columns)
   if (value.substr(0,4)=="All ") nextRow = document.getElementById('result');
   else nextRow = tid.rows[n+1];
/*   if (window.XMLHttpRequest) {
      obj=new XMLHttpRequest();
   }
   else {            
       obj=new ActiveXObject("Microsoft.XMLHTTP");
   }
   if (obj) {
      obj.open("GET", "?nextFindOption="+name+"&value="+value, false);
     obj.send(null);
     nextRow.cells[nextRow.cells.length-1].innerHTML = obj.responseText;
   }
*/
   nextRow.cells[nextRow.cells.length-1].innerHTML = 
      ajax("?nextFindOption="+name+"&value="+value);
   t.blur();
   nextRow.style.display="";
}

function updateOrDelete(cmd, form) {
   if (confirm('Are you sure you want to '+cmd+' the information?')) {
     document.forms[form].action.value=cmd;
      document.getElementById(form).submit();
   }
}

function show(id) {
   document.getElementById(id).style.display='block';
}

function hide(id) {
   document.getElementById(id).style.display='none';
}
function test() {alert(33);}
function getInfo(action, id, description) {
alert(1);
   switch (action) {
      case "addsubcategory": case "rename":
         if (action=="addsubcategory") {
            header = "Enter label for the subcategory and click submit";
            name = "label";
         }
         else if (action=="rename") {
            header = "Enter new label and click submit";
            name = "label";
         }
         document.getElementById('d2').innerHTML =
            "<h3>"+description+"</h3>"+
            "<form><input type='hidden' name='pos' value='"+id+"'>"+
            "<input type='hidden' name='action' value='"+action+"'>"+
            "<table class='table1' style='border:0px'>"+
            "<tr><th>"+header+"</th></tr>"+
            "<tr><td style='text-align:center'><input type='text' size='30' name='"+name+"'></td></tr><tr><td style='text-align:center'><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href=\"javascript:void()\" onClick=\"document.getElementById('d2').innerHTML='';document.getElementById('select1').options[0].selected='selected';return false;\">cancel</a></td></tr></table></form>";
         break;

      case "delete": case "deletedescendants":
         document.getElementById('d2').innerHTML =
            "<h3>"+description+"</h3>"+
            "<form><input type='hidden' name='pos' value='"+id+"'>"+
            "<input type='hidden' name='action' value='"+action+"'>"+
            "<table class='table1' style='border:0px'>"+
            "<tr><th>This operation is not reversible!<br />Are you sure you want to "+description+"<br /><input type='submit' value='Yes'>&nbsp;&nbsp;&nbsp;<a href=\"javascript:void()\" onClick=\"document.getElementById('d2').innerHTML='';document.getElementById('select1').options[0].selected='selected';return false;\">cancel</a></td></tr></table></form>";
         break;

      case "attachdocument":
//            "<tr class='tr1'><th> Please complete all the steps</th></tr>"+
         document.getElementById('d2').innerHTML =
            "<h3 style='margin-bottom:0px'>"+description+"</h3>"+
            "<form method='post' enctype='multipart/form-data'><div style='border:1px solid black'><input type='hidden' name='pos' value='"+id+"'>"+
            "<input type='hidden' name='action' value='"+action+"'>"+
            "<table class='table1' style='border:0px'>"+
            "<tr class='tr2'><td class='td1' style='text-align:left;padding:15px;'>1. Enter a label for this attachment: "+
            "<input type='text' name='label' size='20'><br />"+
            "</td></tr>"+
            "<tr class='tr2' style='background-color:#F0F0F0'><td class='td1' style='text-align:left;padding:15px'>2. What is the form of the attachment?<br />"+
            "<input type='radio' name='docType' value='file' onClick=\"show('dT1');hide('dT2')\"> It is a file that I want to upload.<br /><div id='dT1' style='display:none'>File:<input type='file' name='uploadFile' size=30'></div>"+
            "<input type='radio' name='docType' value='url' onClick=\"show('dT2');hide('dT1')\"> It is a resource on the internet and I want to specify the URL/link.<div id='dT2' style='display:none'>URL:<input type='text' name='url' size=80'></div>"+
            "</td></tr>"+
            "<tr class='tr2'><td class='td1' style='text-align:left;padding:15px'>3. Document searching is supported on uploaded text files or URL (link) to text files including HTML files. Searching is possible on other types of files but may not be 100% accurate. For such a case, you may upload an additional keywords text file that will be used to support searches on the non-text file attachment.<br /><br />"+
            "Do you want to upload a keywords text file for search support?<br />"+
            "<input type='radio' name='search' value='yes' onClick=\"show('s1')\"> Yes<br /><div id='s1' style='display:none'>File:<input type='file' name='searchFile' size=30'></div>"+
            "<input type='radio' name='search' value='no' onClick=\"hide('s1')\"> No, because the document or URL is already a textfile or I do not want searches on the document."+
            "</td></tr>"+
            "</table></div>"+
            "<input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href=\"javascript:void()\" onClick=\"document.getElementById('d2').innerHTML='';document.getElementById('select1').options[0].selected='selected';return false;\">cancel</a></form>";
         break;

      case "attachnote":
         document.getElementById('d2').innerHTML =
            "<h3 style='margin-bottom:0px'>"+description+"</h3>"+
            "<form method='post' enctype='multipart/form-data'><div style='border:1px solid black'><input type='hidden' name='pos' value='"+id+"'>"+
            "<input type='hidden' name='action' value='"+action+"'>"+
            "<table class='table1' style='border:0px'>"+
            "<tr class='tr2'><td class='td1' style='text-align:left;padding:15px;'>1. Enter a label or title for this note: "+
            "<input type='text' name='label' size='20'><br />"+
            "</td></tr>"+
            "<tr class='tr2' style='background-color:#F0F0F0'><td class='td1' style='text-align:left;padding:15px'>2. Enter the note.<br />"+
            "<textarea name='note' rows=15 cols=60></textarea>"+
            "</td></tr>"+
            "<tr class='tr2'><td class='td1' style='text-align:left;padding:15px'>3. Document searching is supported on uploaded text files or URL (link) to text files including HTML files. Searching is possible on other types of files but may not be 100% accurate. For such a case, you may upload an additional keywords text file that will be used to support searches on the non-text file attachment.<br /><br />"+
            "Do you want to upload a keywords text file for search support?<br />"+
            "<input type='radio' name='search' value='yes' onClick=\"show('s1')\"> Yes<br /><div id='s1' style='display:none'>File:<input type='file' name='searchFile' size=30'></div>"+
            "<input type='radio' name='search' value='no' onClick=\"hide('s1')\"> No, because the document or URL is already a textfile or I do not want searches on the document."+
            "</td></tr>"+
            "</table></div>"+
            "<input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href=\"javascript:void()\" onClick=\"document.getElementById('d2').innerHTML='';document.getElementById('select1').options[0].selected='selected';return false;\">cancel</a></form>";
         break;


      case "addquestion":
         document.getElementById('d2').innerHTML =
            "<h3 style='margin-bottom:0px'>"+description+"</h3>"+ajax("?action="+action+"&id="+id+"&prompt=yes");
         break;
/*
      case "showall":
         location.href="?action="+action+"&pos="+id;
         break;
      case "getproperties":
         location.href="?action="+action+"&pos="+id;
*/
      default:
         location.href="?action="+action+"&pos="+id;
   }
}

function addPrivilege() {
   t = document.getElementById('pTable');
   r = t.insertRow(-1);
   c1 = r.insertCell(0);
   c2 = r.insertCell(1);
   c1.innerHTML = "Group's Name:";
   c2.innerHTML = "<input type='text' name='newGps[]' size=10>";
   r = t.insertRow(-1);
   c1 = r.insertCell(0);
   c2 = r.insertCell(1);


   c1.innerHTML = "Privilege:";
   c2.innerHTML = "<select name='newPrvs[]'><option>0<option>1<option>2<option>3";
   r = t.insertRow(-1);
   c1 = r.insertCell(0);
   c2 = r.insertCell(1);
   c1.innerHTML = "Member myEmich Email<br />addresses (comma separated):";
   c2.innerHTML = "<textarea name='members[]' rows=4 cols=18></textArea>";
}
