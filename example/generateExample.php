<?php

/**
 * Generates an example-file for import-tests based on the Personio position XML-format.
 */

// max jobs to generate
$maxJobs = 1000;

// default XML with placeholders
$defaults = '<position>
            <id>[ID]</id>
            <office>[OFFICE]</office>
            <recruitingCategory>[RECR]</recruitingCategory>
            <name>[NAME]</name>
            <jobDescriptions>
                <jobDescription>
                    <name>Your mission</name>
                    <value>
<![CDATA[
                                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.<br><ul><li>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</li><li>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</li><li>At vero eos et accusam et justo duo dolores et ea rebum.</li><li>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</li></ul>
]]>
                    </value>
                </jobDescription>
                <jobDescription>
                    <name>Your profile</name>
                    <value>
<![CDATA[
                                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.<br><ul><li>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</li><li>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</li><li>At vero eos et accusam et justo duo dolores et ea rebum.</li><li>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</li></ul>
]]>
                            </value>
               </jobDescription>
               <jobDescription>
                  <name>Why us?</name>
                  <value>
<![CDATA[
                                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.<br><ul><li>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</li><li>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</li><li>At vero eos et accusam et justo duo dolores et ea rebum.</li><li>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</li></ul>
]]>
                    </value>
                </jobDescription>
            </jobDescriptions>
            <employmentType>[EMPLOYMENTTYPE]</employmentType>
            <seniority>entry-level</seniority>
            <schedule>full-time</schedule>
            <yearsOfExperience>lt-1</yearsOfExperience>
            <occupation>work_at_home</occupation>
            <occupationCategory>other</occupationCategory>
            <createdAt>[DATE]</createdAt>
        </position>';

// offices
$offices = [
    'Berlin',
    'Köln',
    'Ludwigsburg',
    'Lissabon',
    'Leipzig',
    'London',
    'Dresden',
    'Lausen',
    'Musterstadt',
];

// employment type
$employment_type = [
    'permanent',
    'intern',
    'trainee',
    'freelance'
];

// recruiting category
$recruitingCategory = [
    'Permanent Employee',
    'Cooperative Employee',
    'Employer',
    'Internship',
    'Job',
    'Labour hire',
    'Supervisor',
    'Volunteering'
];

$output = '<?xml version="1.0" encoding="UTF-8"?><workzag-jobs>';
for( $i = 0;$i < $maxJobs; $i++ ) {
    $xml = $defaults;
    $randomNumber = rand(0,1000000);
    $xml = str_replace("[ID]", $randomNumber, $xml);
    $xml = str_replace("[NAME]", "Jobname Number ".$randomNumber, $xml);
    $xml = str_replace("[DATE]", date('Y-m-d').'T'.date('H:i:s').'+00:00', $xml);
    $xml = str_replace("[OFFICE]", $offices[array_rand($offices, 1)], $xml);
    $xml = str_replace("[EMPLOYMENTTYPE]", $employment_type[array_rand($employment_type, 1)], $xml);
    $xml = str_replace("[RECR]", $recruitingCategory[array_rand($recruitingCategory, 1)], $xml);
    $output .= $xml;
}
$output .= '</workzag-jobs>';

file_put_contents( "xml/example.xml", $output);

echo "done";