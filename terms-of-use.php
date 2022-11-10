<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
include('array.php');
// session_start();
//if(!empty($_GET['search']) || !empty($_GET['id'])){
if (empty($_GET['category'])) $_GET['category'] = 1;


if (!empty($_GET['id'])) {
  $post_data['id'] = $_GET['id'];
}

if (!empty($_GET['category'])) {
  $post_data['category'] = $_GET['category'];
}

if (!empty($_GET['page'])) {
  $post_data['page'] = $_GET['page'];
} else $post_data['page'] = 1;

if ($_GET['page'] > 9) {
  $page_pagination = $post_data['page'] + 1;
} else $page_pagination = 11;

if (!empty($_GET['subcategory'])) {
  $post_data['subcategory'] = $_GET['subcategory'];
} else {
  $post_data['limit'] = 9;
}

if (!empty($_GET['search'])) {
  $post_data['keyword'] = $_GET['search'];
  $post_data['ajax'] = $_GET['ajax'];
  $post_data['limit'] = $_GET['limit'];
}

$post_data = http_build_query($post_data);
// print_r($post_data);
// die;
$url = 'https://apiadmin.qienergy.ai/api/frequencies';
$res = 'curl_post'($url . '?' . $post_data, '', $header);
$response = json_decode($res['res']);
$frequencies = $response->frequencies;
//print_r($frequencies);
// echo '<br><br><b>URL:</b> ' . $url . "\n\n<br/><br><b>POST DATA:</b> " . $post_data . "\n\n<br/><br><b>RES:</b>" . $res['res'];
// exit;
foreach ($SUBCATEGORIES as $v) { //print_r($v);
  if ($v->id == $_GET['subcategory']) {
    $subcategory = $v->name;
  }
}
if (!empty($_GET['search'])) {
  echo $res['res'];
  die;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<title>Terms-of-use - <?php echo $GLOBALS['SITENAME']?></title>
  <?php include 'head.php'; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>
<?php include 'header.php'; ?>


<div class="container">
  <div class="row">
<article class="post-4692 page type-page status-publish pmpro-has-access ast-article-single" id="post-4692" itemtype="https://schema.org/CreativeWork" itemscope="itemscope">
		<header class="entry-header ast-no-thumbnail ast-no-meta">
		
		<h1 class="entry-title" itemprop="headline">Terms of Use</h1>	</header><!-- .entry-header -->

	<div class="entry-content clear" itemprop="text">

		
		
<p>By using the Qi Energy Ai Website or Services in any manner, you are bound by these Terms and Conditions of Service. If you do not agree to the Terms and Conditions, then do not use the Website or Services. If you are accepting these terms and Conditions on behalf of an individual, a family member or friend, company, organization, academic institution, government, or other legal entity, you represent and warrant that (a) you are authorized to do so, (b) the entity agrees to be legally bound by the Terms and Conditions, and (c) neither you nor the entity is barred from using the Services or accepting the Terms and Conditions under the laws of the applicable jurisdiction. Please read these Terms and Conditions (“Agreement”, “Terms and Conditions”) carefully before using qienergyai.com (“the Site”) operated by Qi Energy Ai (“us”, “we”, or “our”). This Agreement sets forth the legally binding terms and conditions for your use of the Site. By accessing or using the Site in any manner, including, but not limited to, visiting or browsing the Site or contributing content or other materials to the Site, you agree to be bound by these Terms and Conditions. Capitalized terms are defined in this Agreement. These Terms of Use may be updated from time to time. If you do not agree to these Terms of Use, please do not use this website. Occasionally refer back to these terms and conditions as they may periodically be updated.</p>
<h4 id="register-for-the-free-qi-energy-ai-trials-prior-to-becoming-a-paying-subscriber">REGISTER FOR THE FREE QI ENERGY AI TRIALS PRIOR TO BECOMING A PAYING SUBSCRIBER</h4>
<p>Everyone is encouraged to register their phone number for the FREE 7 Day Trial. All Qi Energy Ai Signatures are administered remotely by sending signals in their registered phone numbers. Hence the person whose phone is registered receives the Energy Signature transmission of the quantum energy.</p>
<p>The Qi Energy Ai services offered at <a href="http://www.qienergy.ai">WWW.QIENERGY.AI</a> do NOT require anyone to agree or submit to any express or implied contract, written agreement, verbal agreement, etc.</p>
<p>in order to participate in the FREE TRIALS or as a paying subscriber. All subscribers at <a href="http://www.qienergy.ai">WWW.QIENERGY.AI</a> reserve the unilateral right to discontinue their monthly subscription at any time at their discretion. The subscriber has full control over their participation in any Qi Energy Ai session.&nbsp;</p>
<h4 id="intellectual-property">Intellectual Property</h4>
<p>The Site and its original content, features, and functionality are owned by QiEnergyAi.com and are protected by international copyright, trademark, patent, trade secret, and other intellectual property or proprietary rights laws.</p>
<h4 id="termination">Termination</h4>
<p>We may terminate your access to the Site, without cause or notice, which may result in the forfeiture and destruction of all information associated with you. All provisions of this Agreement by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity, and limitations of liability.</p>
<h4 id="links-to-other-sites">Links to Other Sites</h4>
<p>Our Site may contain links to third-party sites that are not owned or controlled by QiEnergy.Ai.</p>
<p><a href="/">QiEnergy.Ai</a> has no control over and assumes no responsibility for, the content, privacy policies, or practices of any third-party sites or services. We strongly advise you to read the terms and conditions and privacy policy of any third-party site that you visit.</p>
<h4 id="subscription-policy">Subscription Policy&nbsp;</h4>
<p>Please register initially for the FREE TRIAL at the top of the home page: WWW.QIENERGY.AI Or contact our support desk to receive your complimentary FREE TRIAL: E-mail: <a href="mailto:support@qienergy.ai">support@qienergy.ai</a>. All potential Qi Energy Ai subscribers are initially required to experience the Qi Energy Ai sessions by way of the FREE TRIAL that is available to anyone in the world. The FREE TRIAL session is without obligation and is found on the home page: <a href="/">WWW.QIENERGY.AI</a>. Only after experiencing the FREE TRIAL is an individual consider paying for Qi Energy Ai sessions.</p>
<h4 id="cancellation-policy-for-re-occurring-subscription">Cancellation Policy for Re-occurring subscription</h4>
<p>The client must cancel their re-occurring subscription five (5) business days prior to the billing date to avoid unwanted billing.</p>
<h4 id="disclaimer">Disclaimer</h4>
<p>WWW.QIENERGY.AI does NOT sell nor recommend any physical product, such as a supplement, vitamin, nutraceutical, hormone, mineral, phytochemical, natural food, digestive enzyme, drug, endorphin, neurotransmitter, etc. All Qi Energy Ai sessions are exclusively administered in the quantum dimension or Qi Energy Signatures dimension upon photographs of people, animals, plants, or objects.</p>
<p>Qi Energy Ai is a new and emerging science and thus the terms, words, descriptions, theories, test results, sessions, observations, statements, interpretations, projections, claims, conclusions, testimonies, diagnostic test results, etc. on the website WWW.QIENERGY.AI reflect the new and emerging Qi Energy Signatures language necessary to describe this new branch of physics. These aforesaid terms and expressions do not have a linguistic equivalence on account of the research done with Qi Energy Signatures. Hence, the information found on WWW.QIENERGY.AI is exclusive to Qi Energy Ai science and does not have a linguistic equivalence to any other religious, scientific or philosophical discipline. In short, the new and emerging science of Qi Energy Ai demands a new and emerging language. Qi Energy Ai is not to be construed as Newtonian physics, the non-General Theory of Relativity, or the General Theory of Relativity.</p>
<p>Qi Energy Ai honors the medical community and encourages everyone to likewise respect and honor the advice of qualified medical and wellness professionals. Additionally, Qi Energy Ai honors the scientific community and encourages everyone to likewise respect and honor the advice of qualified scientists and researchers.</p>
<h4 id="changes-to-this-agreement">Changes to This Agreement</h4>
<p>Please review the Agreement and Terms &amp; Conditions periodically for changes. Qi Energy Ai retains to right to change, alter or eliminate any part of the Agreement or Terms &amp; Conditions at any time. If you do not agree to or understand the Agreement or Terms &amp; Conditions then please refrain from submitting any photograph of a person, animal, plant, or object to receiving the Qi Energy Ai transmission of non-physical energy.&nbsp;Furthermore, if you do not agree to or understand the Agreement or Terms &amp; Conditions, then do not use, access, or continue to visit the website: WWW.QIENERGY.AI</p>

<h4 id="medical-records-personal-information">Medical Records &amp; Personal Information</h4>
<p>Please do NOT e-mail, text, communicate or provide any medical records or personal information to Qi Energy Ai. Your medical records or personal information will be promptly deleted as per our privacy policy. Qi Energy Ai does not retain any medical records or personal information of any subscriber. Furthermore, Qi Energy Ai does not treat, cure, diagnose or provide health solutions to people, animals, plants, or objects. Rather, Qi Energy Ai transmits Qi Energy Ai upon the energetic pattern body found on photographs of people, animals, plants, or objects.&nbsp;&nbsp;</p>

<p>It is your exclusive obligation to maintain and control passwords to your Qi Energy Ai account. You are exclusively responsible for all activities that occur in connection with your user name and password. You agree to immediately notify WWW.QIENERGY.AI of any unauthorized uses of your user name and password or any other breaches of security. WWW.QIENERGY.AI will not be liable for any loss or damages of any kind, under any legal theory, caused by your failure to comply with the foregoing security obligations or caused by any person to whom you grant access to your account.</p>

		
</div>
</article>
</div>

</div>
</body>

</html>