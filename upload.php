<?php
$title = "Video Upload - American Immigration Council";
include('header.php');

?>
<div class="container">
   <div class="videoUpload__wrapper">
       <h1 class="page-title">Upload Video</h1>
       <form action="postsubmit.php" onsubmit="showLoadingModal()" method="POST" enctype="multipart/form-data" id="videoUpload" name="videoUpload">
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
                	<label for="state">Topic(s)</label>
					<input name="topics" id="topics">
				</div>
                <div class="input-group">
                    <label for="locations">Location</label>
                    <input type="text" name="locations" id="locations">
                </div>
				<div class="input-group">
                    <label for="tags">Tags</label>
					<span class="input-group__description">Please enter tags with commas separating them.</span>
                    <input type="text" name="tags" id="tags">
                </div>

                <div class="aic-button__container">
                   <input type="submit" value="Submit" name="submit" class="aic-button aic-button--primary aic-button--large">
                </div>
       </form>
   </div>
</div>

<div class="loading-modal">
<img class="spin" src="./jeremy-head.jpg" />
<p style="margin-top:2rem;">Video Uploading...<br><br> Please don't close this page until finished or JR will be very sad.</p>
</div>

<script>
function showLoadingModal(){
document.querySelector(".loading-modal").classList.add("show");
}

var tags = document.querySelector("input[name='tags']");
new Tagify(tags);

var inputLocationTags = document.querySelector('input[name="locations"]'),
    // init Tagify script on the above inputs
    tagifyLocationTags = new Tagify(inputLocationTags, {
        whitelist:
            ['Alabama','Alaska','American Samoa','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Federated States of Micronesia','Florida','Georgia','Guam','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Marshall Islands','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Northern Mariana Islands','Ohio','Oklahoma','Oregon','Palau','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Island','Virginia','Washington','West Virginia','Wisconsin','Wyoming'
],
        maxTags: 10,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions
            classname: 'tags-look', // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
        }
    })

var inputTopicTags = document.querySelector('input[name="topics"]'),
    // init Tagify script on the above inputs
    tagifyTopicTags = new Tagify(inputTopicTags, {
        whitelist: [
  "Immigration 101",
  "How Imm. System Works",
  "History of Immigration",
  "Demographics",
  "Elections",
  "Birthright Citizenship",
  "Immigration and Crime",
  "Immigration Reform",
  "Executive Action",
  "Legislation",
  "Immigration at the Border",
  "Abuses",
  "Border Enforcement",
  "Detention",
  "Interior Enforcement",
  "State and Local",
  "Refugees + Asylum Seekers",
  "Asylum",
  "Refugee Status",
  "Work Authorization",
  "Waivers and Relief from Deportation",
  "Economic Impact",
  "Employment and Wages",
  "Family-Based Immigration",
  "Integration",
  "State by State",
  "Taxes & Spending Power",
  "Undocumented Immigrants",
  "The Legal System",
  "Federal Courts/Jurisdiction",
  "Immigration Courts",
  "Right to Counsel",
  "Civic Engagement",
  "Civil Dialogue",
  "Public Attitudes",
  "Behavioral Science",
  "Culture Change",
  "Social Cohesion",
  "Political Polarization",
  "Bridge Building",
  "Imm. Benefits and Relief",
  "Adjustment of Status",
  "Child Status Protection Act",
  "DACA/DAPA",
  "Temporary Protected Status",
  "Business and the Workforce",
  "Employment Based",
  "Entrepreneurship/Innovation",
  "High Skilled",
  "Low Wage",
  "Global Competitiveness",
  "Industries",
  "Healthcare",
  "Hospitality & Tourism",
  "Innovation & STEM Fields",
  "International Students",
  "Labor-Intensive Industries",
  "Agriculture"
],
        maxTags: 10,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions
            classname: 'tags-look', // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
        }
    })
</script>

<?php
phpinfo();
?>