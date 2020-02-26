<?php
    function AssignKlasToStudent()
    {
        TeacherSessionActive();
        ValidateParameters(array("StudentID","KlasID"));

        $quary = "UPDATE `users` SET `klas` = ? WHERE `ID`= ?";
        ExecuteSql($quary,array($_POST['KlasID'],$_POST['StudentID']));
        die(new Response(ResponseTypes::succeeded, "Student has been assigned to a Klas"));
        
    }