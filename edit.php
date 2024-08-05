<?php
require_once 'inc/classes/Constants.php';
require 'inc/classes/DynamoDBWrapper.php';
require 'inc/classes/S3Wrapper.php';

// If video query string is blank, redirect to index page
if($_GET['v'] == ''){
    header('Location: '. '/');
}

$dynamoDB = new DynamoDBWrapper(Constants::$region, Constants::$version);
$dynamoClient = $dynamoDB->getDynamoClient();

$s3 = new S3Wrapper(Constants::$region);
$s3Client = $s3->getS3Client();

// Get video ID from query string
$videoID = $_GET['v'];
echo "<script>const videoID = '$videoID';</script>";

edit_submit();

$result = $dynamoClient->getItem(array(
    'ConsistentRead' => true,
    'TableName' => 'councilVideos',
    'Key'       => array(
        'videoID'   => array('S' => $videoID),
    )
));

// Video vars
$videoTitle = $result['Item']['title']['S'];
$videoDescription = $result['Item']['description']['S'];

if(array_key_exists("topics", $result['Item'])){
    echo "<br>";
    $videoTopics = $result['Item']['topics']['SS'];
}

if(array_key_exists("locations", $result['Item'])){
$videoLocations = $result['Item']['locations']['SS'];
echo "<br>";
}

if(array_key_exists("tags", $result['Item'])){
$videoTags = $result['Item']['tags']['SS'];
}

$cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => Constants::$bucketName,
    'Key' => $videoID . '/' . $videoID . '-thumbnail.jpg'
    ]
);

$thumbnailURL = $s3Client->createPresignedRequest($cmd, '+10 minutes');

// Get the actual presigned-url
$thumbnailURL = (string)$thumbnailURL->getUri();

$title = 'Edit ' . $videoTitle . ' - American Immigration Council';

function edit_submit(){
if(isset($_POST['submit'])){
    $dynamoDB = new DynamoDBWrapper(Constants::$region, Constants::$version);
    $dynamoClient = $dynamoDB->getDynamoClient();

    $newVideoTitle = $_POST['title'];
    $newVideoTitleLowercase = strtolower($newVideoTitle);

    $newVideoDescription = $_POST['description'];
    $newVideoDescriptionLowercase = strtolower($newVideoDescription);

    $videoID = $_GET['v'];

    $expressionAttributeValuesArray = [];
    $expressionAttributeValuesArray = [
        ':newVideoTitle' => [
            'S' => $newVideoTitle
        ],
        ':newVideoTitleLowercase' => [
            'S' => $newVideoTitleLowercase
        ],
        ':newVideoDescription' => [
            'S' => $newVideoDescription
        ],
        ':newVideoDescriptionLowercase' => [
            'S' => $newVideoDescriptionLowercase
        ]
    ];

    $updateExpressionStatement = "SET title = :newVideoTitle, titleLowercase = :newVideoTitleLowercase, description = :newVideoDescription, descriptionLowercase = :newVideoDescriptionLowercase";

    $attributesToRemove = array();

    $attributes = ["topics", "locations", "tags"];
    $attributesLowercase = ["topicsLowercase", "locationsLowercase", "tagsLowercase"];

    foreach ($attributes as $attribute) {
        if(strlen($_POST[$attribute]) == 0){
            array_push($attributesToRemove, $attribute);
            array_push($attributesToRemove, $attribute . "Lowercase");
        } else {
            $newValues = array_column(json_decode($_POST[$attribute]), 'value');
            $newValuesLowercase = array_map('strtolower', array_column(json_decode($_POST[$attribute]), 'value'));

            $expressionAttributeValue = ":newVideo" . ucfirst($attribute);
            $expressionAttributeValueLowercase = ":newVideo" . ucfirst($attribute) . "Lowercase";

            $updateExpressionStatement .= ", " . $attribute . " = " . $expressionAttributeValue . ", " . $attribute . "Lowercase" . " = " . $expressionAttributeValueLowercase;

            $expressionAttributeValuesArray[$expressionAttributeValueLowercase]["SS"] = $newValuesLowercase;
            $expressionAttributeValuesArray[$expressionAttributeValue]["SS"] = $newValues;
        }
    }

    if($attributesToRemove){
        $removeExpressionStatement = " REMOVE " .  implode(', ', $attributesToRemove);
        $updateExpressionStatement .= $removeExpressionStatement;
    } else {
    }

    $response = $dynamoClient->updateItem(array(
        'TableName' => 'councilVideos',
        'Key' => [
            'videoID' => ['S' => $videoID]
        ],
        'ExpressionAttributeValues' => $expressionAttributeValuesArray,
        'UpdateExpression' => $updateExpressionStatement
    ));
}
}

include('header.php');
?>

<div class="container">

   <div class="videoEdit__wrapper">
   <h1>Edit Video</h1>
   <img class="videoEdit__thumbnail" src="<?php echo $thumbnailURL; ?>">
       <form action="./edit.php?v=<?php echo $videoID; ?>" method="POST" enctype="multipart/form-data" id="videoEdit" name="videoEdit">
       <div class="input-group">
                    <label for="title">Title</label>
                    <input required type="text" name="title" id="title" value="<?php echo $videoTitle; ?>">
                </div>
                <div class="input-group">
                    <label for="description">Description</label>
                    <input required type="text" name="description" id="description" value="<?php echo $videoDescription; ?>">
                </div>

                <div class="input-group">
                    <label for="topics">Topics</label>
                    <input type="text" name="topics" id="topics" >
                </div>

                <div class="input-group">
                    <label for="locations">Locations</label>
                    <input type="text" name="locations" id="locations">
                </div>

                <div class="input-group">
                    <label for="tags">Tags</label>
                    <input type="text" name="tags" id="tags">
                </div>


                <a href="#" onclick="myFunction()" class="delete-text">
<svg style="position:relative; top:10px;" fill="#FF2600" width="32px" height="32px" version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
 <path d="m77.828 32h-12.828l-1.9297-6.75c-1.2266-4.2852-5.1406-7.2422-9.6016-7.25h-6.9375c-4.4688-0.003906-8.3945 2.957-9.6211 7.25l-1.9102 6.75h-12.828c-2.1094-0.042969-3.9102 1.5156-4.1719 3.6094-0.10938 1.1289 0.26172 2.2461 1.0234 3.0859 0.76172 0.83594 1.8438 1.3086 2.9766 1.3047h3c0 0.3125 0.015625 0.62109 0.050781 0.92969l3.7891 32.23c0.58984 5.043 4.8633 8.8438 9.9414 8.8398h22.438c5.0703 0 9.3398-3.7969 9.9297-8.8281l3.7891-32.23h0.003906c0.035156-0.3125 0.058594-0.62891 0.058594-0.94141h3c1.1328 0.003906 2.2148-0.46875 2.9766-1.3047 0.76172-0.83984 1.1328-1.957 1.0234-3.0859-0.26172-2.0938-2.0625-3.6523-4.1719-3.6094zm-33.227-4.5508c0.24609-0.85938 1.0312-1.4531 1.9297-1.4492h6.9492c0.89062 0 1.6758 0.59375 1.918 1.4492l1.3008 4.5508h-13.398zm20.867 25.551-0.94141 8-1.3164 11.23c-0.11719 1.0117-0.98047 1.7773-2 1.7695h-22.43c-1.0234 0.007812-1.8828-0.75781-2-1.7695l-1.3125-11.23-0.94141-8-1.5273-13h34z"/>
</svg>
 Delete Video</a>

                <div class="aic-button__container" style="margin-top:2rem">
                   <input type="submit" value="Submit" name="submit" class="aic-button aic-button--primary aic-button--large">
                </div>
       </form>
   </div>
</div>

<p class="message"></div>

<footer></footer>

<script>
var topicsEl = document.querySelector("input[name='topics']");
var tagify = new Tagify(topicsEl, {
        whitelist: ["Immigration 101","How Imm. System Works","History of Immigration","Demographics","Elections","Birthright Citizenship","Immigration and Crime","Immigration Reform","Executive Action","Legislation","Immigration at the Border","Abuses","Border Enforcement","Detention","Interior Enforcement","State and Local","Refugees + Asylum Seekers","Asylum","Refugee Status","Work Authorization","Waivers and Relief from Deportation","Economic Impact","Employment and Wages","Family-Based Immigration","Integration","State by State","Taxes & Spending Power","Undocumented Immigrants","The Legal System","Federal Courts/Jurisdiction","Immigration Courts","Right to Counsel","Civic Engagement","Civil Dialogue","Public Attitudes","Behavioral Science","Culture Change","Social Cohesion","Political Polarization","Bridge Building","Imm. Benefits and Relief","Adjustment of Status","Child Status Protection Act","DACA/DAPA","Temporary Protected Status","Business and the Workforce","Employment Based","Entrepreneurship/Innovation","High Skilled","Low Wage","Global Competitiveness","Industries","Healthcare","Hospitality & Tourism","Innovation & STEM Fields","International Students","Labor-Intensive Industries","Agriculture"
],
        maxTags: 10,
        dropdown: {
            maxItems: 20,           // <- mixumum allowed rendered suggestions
            classname: 'tags-look', // <- custom classname for this dropdown, so it could be targeted
            enabled: 0,             // <- show suggestions on focus
            closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
        }
    })
<?php if(isset($videoTopics)){ ?>
var topicsfromDB = <?php echo json_encode($videoTopics); ?>;
tagify.addTags(topicsfromDB);
<?php } ?>;

var locationsEl = document.querySelector("input[name='locations']");
var tagify = new Tagify(locationsEl, {
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
});
<?php if(isset($videoLocations)){ ?>
var locationsfromDB = <?php echo json_encode($videoLocations); ?>;
tagify.addTags(locationsfromDB);
<?php } ?>


var tagsEl = document.querySelector("input[name='tags']");
var tagify = new Tagify(tagsEl);
<?php if(isset($videoTags)){ ?>
var tagsfromDB = <?php echo json_encode($videoTags); ?>;
tagify.addTags(tagsfromDB);
<?php } ?>

function myFunction() {
    if(confirm("Are you sure you want to delete this video?") == true);{
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajaxHandler.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert('Video has been successfully deleted');
                    } else {
                        alert('An error occurred while executing the PHP function.');
                    }
                }
            };

            var arg1 = encodeURIComponent(videoID);
            var params = "videoID=" + arg1;
            xhr.send(params);
        };
}
</script>
</body>
</html>