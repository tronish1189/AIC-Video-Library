<?php
$title = "Video Upload - American Immigration Council";
include('header.php');
?>
<div class="container">
   <div class="videoUpload__wrapper">
       <h1 class="page-title">Upload Video</h1>
       <form action="postsubmit.php" method="POST" enctype="multipart/form-data" id="videoUpload" name="videoUpload">
       <div class="input-upload-file">
           <label for="file" class="custom-file-upload">
               File Upload
           </label>
           <input id="file" type="file" name="file"/>
       </div>
       <div class="input-group">
                    <label for="title">Title</label>
                    <input required type="text" name="title" id="title">
                </div>
                <div class="input-group">
                    <label for="description">Description</label>
                    <input type="text" name="description" id="description">
                </div>
				<div class="input-group">
                    <label for="tags">Tags</label>
					<span class="input-group__description">Please enter tags with commas separating them.</span>
                    <input required type="text" name="tags" id="tags">
                </div>
                <div class="input-group">
                <label for="state">Tag by state:</label>
                <select id="state" name="state">
                <option value="n/a">N/A</option>
	<option value="AL">Alabama</option>
	<option value="AK">Alaska</option>
	<option value="AZ">Arizona</option>
	<option value="AR">Arkansas</option>
	<option value="CA">California</option>
	<option value="CO">Colorado</option>
	<option value="CT">Connecticut</option>
	<option value="DE">Delaware</option>
	<option value="DC">District Of Columbia</option>
	<option value="FL">Florida</option>
	<option value="GA">Georgia</option>
	<option value="HI">Hawaii</option>
	<option value="ID">Idaho</option>
	<option value="IL">Illinois</option>
	<option value="IN">Indiana</option>
	<option value="IA">Iowa</option>
	<option value="KS">Kansas</option>
	<option value="KY">Kentucky</option>
	<option value="LA">Louisiana</option>
	<option value="ME">Maine</option>
	<option value="MD">Maryland</option>
	<option value="MA">Massachusetts</option>
	<option value="MI">Michigan</option>
	<option value="MN">Minnesota</option>
	<option value="MS">Mississippi</option>
	<option value="MO">Missouri</option>
	<option value="MT">Montana</option>
	<option value="NE">Nebraska</option>
	<option value="NV">Nevada</option>
	<option value="NH">New Hampshire</option>
	<option value="NJ">New Jersey</option>
	<option value="NM">New Mexico</option>
	<option value="NY">New York</option>
	<option value="NC">North Carolina</option>
	<option value="ND">North Dakota</option>
	<option value="OH">Ohio</option>
	<option value="OK">Oklahoma</option>
	<option value="OR">Oregon</option>
	<option value="PA">Pennsylvania</option>
	<option value="RI">Rhode Island</option>
	<option value="SC">South Carolina</option>
	<option value="SD">South Dakota</option>
	<option value="TN">Tennessee</option>
	<option value="TX">Texas</option>
	<option value="UT">Utah</option>
	<option value="VT">Vermont</option>
	<option value="VA">Virginia</option>
	<option value="WA">Washington</option>
	<option value="WV">West Virginia</option>
	<option value="WI">Wisconsin</option>
	<option value="WY">Wyoming</option>
</select>
                </div>
                <div class="aic-button__container">
                   <input type="submit" value="Submit" class="aic-button aic-button--primary aic-button--large">
                </div>
       </form>
   </div>
</div>

<script>
var tags = document.querySelector("input[name='tags']");
new Tagify(tags);
</script>