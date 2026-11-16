<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$docid = $_REQUEST["docid"];
$doc = $db->get_var("SELECT name FROM docs WHERE docid='$docid'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ClacksWeb CMS</title>
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
    <h3><?php echo $doc; ?></h3>
    <div id="formarea">
        <h4>Document files</h4>
        <ul>
            <?php
            if ($docfiles = $db->get_results("SELECT * FROM docfiles WHERE docid='$docid'")) {
                foreach ($docfiles as $docfile) {
                    if ($docfile->mime) {
                        echo '<li><strong><a target="_blank" href="download.php?ft=document&file=' . $docfile->docfileid . $docfile->ext . '">' . $docfile->filename . '</a></strong><br />' . $docfile->description . '</li>';
                    } else {
                        echo '<li><strong><a target="_blank" href="' . $docfile->url . '">' . $docfile->url . '</a></strong><br />' . $docfile->description . '</li>';
                    }
                }
            } else {
                echo '<li>No document files uploaded for this document.</li>';
            }
            ?>
        </ul>
        <h4>ClacksWeb links</h4>
        <ul>
            <?php
            if ($pages = $db->get_results("SELECT pagename, title, sectionurl FROM pageXdoc LEFT JOIN content USING(pageid) LEFT JOIN sections USING(sectionid) WHERE docid='$docid'")) {
                foreach ($pages as $page) {
                    echo '<li><strong><a href="/' . $page->sectionurl . '/' . $page->pagename . '/" target="_blank">' . $page->title . '</a></strong></li>';
                }
            } else {
                echo '<li>No links to this document from ClacksWeb.</li>';
            }
            ?>
        </ul>
        <h4>FAQ system links</h4>
        <ul>
            <?php
            $faqs = $db->get_results("SELECT faq AS label, 'f' AS type ,faqs.faqid AS id FROM cms.faq_doc_map LEFT JOIN cms.faqs USING(faqid) WHERE docid='$docid'", ARRAY_A);
            $subjects = $db->get_results("SELECT subject AS label, 's' AS type, subjects.subjectid AS id FROM cms.subject_doc_map LEFT JOIN cms.subjects USING(subjectid) WHERE docid='$docid'", ARRAY_A);
            
            // Check if $faqs and $subjects are null, and set them to empty arrays if so
            $faqs = ($faqs === null) ? array() : $faqs;
            $subjects = ($subjects === null) ? array() : $subjects;
            
            $links = array_merge($faqs, $subjects);
            
            if ($links) {
                foreach ($links as $link) {
                    echo '<li><strong>';
                    if ($link["type"] == 's') {
                        echo '<a href="http://faqs/subject.php?subjectid=' . $link["id"] . '" target="_blank">';
                    } else {
                        echo '<a href="#" onclick="window.open(\'http://faqs/faq.php?faqid=' . $link["id"] . '\', \'faq\', \'width=700,height=600,top=50,left=50,resizable=yes,scrollbars=yes\')">';
                    }
                    echo $link["label"];
                    echo '</a></strong></li>';
                }
            } else {
                echo '<li>No FAQ system links for this document.</li>';
            }
            ?>
        </ul>
        <p class="formbtns"><input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="self.close();"></p>
    </div>
</body>
</html>
