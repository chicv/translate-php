<?php
require_once 'bootstrap.php';

$content = <<<EOF
Since its creation in 2001, Wikipedia has grown rapidly into one of the largest reference websites, 
attracting 374 million unique visitors monthly as of September 2015.[1] 
There are about 70,000 active contributors working on more than 41,000,000 articles in 294 languages. 
As of today, there are 5,398,799 articles in English. Every day, 
hundreds of thousands of visitors from around the world collectively make tens of thousands of edits and create thousands of new articles to augment the knowledge held by the Wikipedia encyclopedia. 
(See the statistics page for more information.) People of all ages, cultures and backgrounds can add or edit article prose, references, 
images and other media here. What is contributed is more important than the expertise or qualifications of the contributor. 
What will remain depends upon whether the content is free of copyright restrictions and contentious material about living people, 
and whether it fits within Wikipedia's policies, including being verifiable against a published reliable source, 
thereby excluding editors' opinions and beliefs and unreviewed research. 
Contributions cannot damage Wikipedia because the software allows easy reversal of mistakes and many experienced editors are watching to help ensure that edits are cumulative improvements. 
Begin by simply clicking the Edit link at the top of any editable page!
EOF;

echo "original:<br/>";
echo "<div style='width:600px;word-wrap: break-word;'>";
echo $content;
echo "</div>";

$content = $tm->complexTranslate($content, 'paragraph');

echo "<br/>translate:<br/>";
echo "<div style='width:600px;word-wrap: break-word;'>";
echo $content;
echo "</div>";
