<?php
$result = '';

if (!empty($_REQUEST['tag-input'])) {
    $openingTags = getOpeningTags($_REQUEST['tag-input']);
    $closingTags = getClosingTags($_REQUEST['tag-input']);

    $closingTagsReverse = array_reverse($closingTags);
    $openingStripped = getStripped($openingTags, 'opening');
    $closingStripped = getStripped($closingTagsReverse, 'closing');

    if (checkTagCount($openingTags, $closingTags)) {
        $matched = true;

        //Check for mismatches
        foreach ($openingStripped as $item => $value) {
            if ($closingStripped[$item] != $value) {
                $matched = false;
                $result .= 'Expected &lt;/' . $value . '&gt; found &lt;/' . $closingStripped[$item] . '&gt; <br>';
            }
        }

        if ($matched) {
            $result = 'Correctly tagged paragraph';
        }
    } else {
        //Has extra closing tag
        if ($closingStripped > $openingStripped) {
            foreach ($closingStripped as $item => $value) {
                if (empty($openingStripped[$item]) || $value != $openingStripped[$item]) {
                    $result = 'Expected # found &lt;/' . $value . '&gt;';
                    break;
                }
            }
        } else {
            foreach ($openingStripped as $item => $value) {
                if (empty($closingStripped[$item]) || $value != $closingStripped[$item]) {
                    $result = 'Expected &lt;/' . $value . '&gt; found #';
                    break;
                }
            }
        }
    }
}

/**
 * Stripes tag
 *
 * @param array $tags
 * @param string $tagType
 *
 * @return array
 */
function getStripped(array $tags, string $tagType): array
{
    $stripped = [];
    foreach ($tags as $tag) {
        $startPos = $tagType === 'opening' ? 1 : 2;
        $stripped[] = substr($tag, $startPos, 1);
    }
    return $stripped;
}

/**
 * Checks if the opening tags match the amount of closing tags
 *
 * @param array $openingTags
 * @param array $closingTags
 *
 * @return bool
 */
function checkTagCount(array $openingTags, array $closingTags): bool
{
    return count($openingTags) == count($closingTags);
}

/**
 * Gets the opening tags from the input string
 *
 * @param string $str
 *
 * @return array
 */
function getOpeningTags(string $str): array
{
    $openingTags = [];
    $pattern = "/<[A-Z]+[^>]*>/";
    preg_match_all($pattern, $str, $openingTags);
    $result = [];
    foreach ($openingTags as $oT) {
        foreach ($oT as $o) {
            $result[] = $o;
        }
    }
    return $result;
}

/**
 * Gets the closing tags from the input string
 *
 * @param string $str
 *
 * @return array
 */
function getClosingTags(string $str): array
{
    $closingTags = [];
    //update the pattern
    $pattern = "/<\/[A-Z]+>/";
    preg_match_all($pattern, $str, $closingTags);
    $result = [];
    foreach ($closingTags as $cT) {
        foreach ($cT as $c) {
            $result[] = $c;
        }
    }
    return $result;
}

?>

<div
  style="font-family: Arial, sans-serif; margin: auto; height: 600px; width: 1000px; position: absolute; top:0; bottom: 0; left: 0; right: 0;">
  <h2>Tag Checker</h2>
  <h3>Sample Input:</h3>
  <ul>
    <li>The following text &lt;C&gt;&lt;B&gt;is centred and in boldface&lt;/B&gt;&lt;/C&gt;</li>
    <li>&lt;B&gt;This &lt;\g&gt;is &lt;B&gt;boldface&lt;/B&gt; in &lt;&lt;*&gt; a&lt;/B&gt; &lt;\6&gt; &lt;&lt;d&gt;sentence</li>
    <li>&lt;B&gt;&lt;C&gt; This should be centred and in boldface, but the tags are wrongly nested &lt;/B&gt;&lt;/C&gt;</li>
    <li>&lt;B&gt;This should be in boldface, but there is an extra closing tag&lt;/B&gt;&lt;/C&gt;</li>
    <li>&lt;B&gt;&lt;C&gt;This should be centred and in boldface, but there is a missing closing tag&lt;/C&gt;</li>
  </ul>

  <h3>Sample Output:</h3>
  <ul>
    <li>Correctly tagged paragraph</li>
    <li>Correctly tagged paragraph</li>
    <li>Expected &lt;/C&gt; found &lt;/B&gt;</li>
    <li>Expected # found &lt;/C&gt;</li>
    <li>Expected &lt;/B&gt; found #</li>
  </ul>

  <form>
    <br>
    <textarea style="width: 300px; height: 100px;" placeholder="Enter input here to validate" name="tag-input"></textarea>
    <br>
    <br>
    <input type="submit" value="Validate"/>
  </form>
  <p id="result"><?= $result; ?></p>
</div>
