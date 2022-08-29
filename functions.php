<?php

/* Return the name of the month by entering a number in the format "01"/"1" */
function nameOfMonth($nrMonth)
{
    $month = "";

    if($nrMonth==1 || $nrMonth=="1" || $nrMonth=="01")
    $month = "Styczeń";

    if($nrMonth==2 || $nrMonth=="2" || $nrMonth=="02")
    $month = "Luty";

    if($nrMonth==3 || $nrMonth=="3" || $nrMonth=="03")
    $month = "Marzec";

    if($nrMonth==4 || $nrMonth=="4" || $nrMonth=="04")
    $month = "Kwiecień";

    if($nrMonth==5 || $nrMonth=="5" || $nrMonth=="05")
    $month = "Maj";

    if($nrMonth==6 || $nrMonth=="6" || $nrMonth=="06")
    $month = "Czerwiec";

    if($nrMonth==7 || $nrMonth=="7" || $nrMonth=="07")
    $month = "Lipiec";

    if($nrMonth==8 || $nrMonth=="8" || $nrMonth=="08")
    $month = "Sierpień";

    if($nrMonth==9 || $nrMonth=="9" || $nrMonth=="09")
    $month = "Wrzesień";

    if($nrMonth==10 || $nrMonth=="10" || $nrMonth=="10")
    $month = "Październik";

    if($nrMonth==11 || $nrMonth=="11" || $nrMonth=="11")
    $month = "Listopad";

    if($nrMonth==12 || $nrMonth=="12" || $nrMonth=="12")
    $month = "Grudzień";
    
    return $month;
}

/* Return 0 at the beginning of string if is $number has one character */
function add_0_onBeginning($number)
{
    if(strlen($number)==1)
    {
        $number = '0'.$number;
    }
    return $number;
}

/* Return bool whether the room is free | 1 -> free | 0 -> occupied  */
function checkingIfRoomIsFree($room_id,$res_since,$res_untill)
{
    // Initialize database connection
    global $conn;

    // Checking if the room has no reservation on the given days
    $sqlCheckIfRoomIsFree = 'SELECT * FROM reservations,rooms
    WHERE
    (
        "'.$res_untill.'" >= date_res_since AND
        "'.$res_since.'" <= date_res_untill
    )
    AND
    room_id = '.$room_id.'
    ';
    $resultIfRoomIsFree = $conn->query($sqlCheckIfRoomIsFree);
    if($resultIfRoomIsFree->num_rows>0)
    {
        return 0;
    }
    if($resultIfRoomIsFree->num_rows==0)
    {
        return 1;
    }

}

/* Function returning 1 -> for a sufficiently large room 0 -> for a too small room */  
function checkingPeopleInRoom($room_id,$how_many_people)
{
    global $conn;

    $sqlCheckPeopleInRoom = 'SELECT how_many_people FROM rooms
    WHERE id = '.$room_id;

    $resultCheckPeopleInRoom = $conn->query($sqlCheckPeopleInRoom);
    while($row = $resultCheckPeopleInRoom->fetch_assoc())
    {
        $MaxPeopleInRoom = $row['how_many_people'];
    }
    if($MaxPeopleInRoom >= $how_many_people)
    {
        return 1;
    }
    if($MaxPeopleInRoom < $how_many_people)
    {
        return 0;
    }
}

/* Function that prints out a session variable with the name specified in the argument and then unset variable */
function writeSessionValueByNameIfExists($ses_var_name)
{
    if(isset($_SESSION[$ses_var_name]))
    {
        $help = $_SESSION[$ses_var_name];
        unset($_SESSION[$ses_var_name]);
        return $help;
    }
}

/* Displays a row with the corresponding content when a room is free or not on a given day */
function dayInCalendar($day,$room_id,$date)
{
    // Checks if the specified day has not already happened
    $todaysDate = date("Y-m-d");
    if($todaysDate>$date)
    {
        echo'
                <td>
                    <button disabled class="calendar-but reserved">'.$day.'</button>
                </td>
            ';
    }
    else
    {
        // The room on the given date is free
        if(checkingIfRoomIsFree($room_id,$date,$date)==1)
        {
            echo'
                    <td>
                        <button name="choose-day" type="submit" value='.$day.' class="calendar-but free">'.$day.'</button>
                    </td>
                ';
        }
        // The room on the given date is occupied
        if(checkingIfRoomIsFree($room_id,$date,$date)==0)
        {
            echo'
                    <td>
                        <button disabled class="calendar-but reserved">'.$day.'</button>
                    </td>
                ';
        }
    }
}

/* Displaying dates selected from the calendar in the form */
function setDayInFormChoseFromCalendar($when,$year,$month)
{
    if($when=="since")
    {
        // Click on a date in the calendar
        if(isset($_POST['choose-day']))// The choose-day variable stores the date from the calendar
        {
            // Set the selected date to a variable
            $choseDate = $year."-".$month."-".$_POST['choose-day'];

            /* The session variable calendarSince holds the date from which we start reservations
               The session variable CalendarUntill stores the date when we end the reservation */
            if(!isset($_SESSION['calendarSince'])&&!isset($_SESSION['calendarUntill']))
            {                  
                // Variable stores date since we assign a selected day from the calendar
                $_SESSION['calendarSince']=$choseDate;

                // Displaying the date in the form 
                return $_SESSION['calendarSince'];
            }

            // If set since and the selected date is earlier than since
            if(isset($_SESSION['calendarSince'])&& $_SESSION['calendarSince']>$choseDate)
            {
                $_SESSION['calendarSince']=$choseDate;
                return $_SESSION['calendarSince'];
            }


            // If the function checks all conditions and still works, and if there is a calendar since, it returns the value
            if(isset($_SESSION['calendarSince']))
            {
                return $_SESSION['calendarSince'];
            }

        }
    }

    if($when=="untill")
    {
        if(isset($_POST['choose-day']))
        {
            $choseDate = $year."-".$month."-".$_POST['choose-day'];
            // If the start is set but the end is not and the selected date is later than the set start
            if(isset($_SESSION['calendarSince'])&&$choseDate>$_SESSION['calendarSince']&&!isset($_SESSION['calendarUntill']))
            {
                $_SESSION['calendarUntill']=$choseDate;
                return $choseDate;
            }

            // If it is set since and the selected date is earlier than since, so that untill does not change too
            if(isset($_SESSION['calendarSince'])&& $_SESSION['calendarSince']>$choseDate && isset($_SESSION['calendarUntill']))
            {
                return $_SESSION['calendarUntill'];
            }


            // If since is set and untill is set and a date greater than since is selected
            if(isset($_SESSION['calendarSince'])&&isset($_SESSION['calendarUntill'])&&$choseDate>$_SESSION['calendarSince'])
            {
                $_SESSION['calendarUntill']=$choseDate;
                return $_SESSION['calendarUntill'];
            }

            // If start is set but not end and same date is given
            if(isset($_SESSION['calendarSince'])&&!isset($_SESSION['calendarUntill'])&&$_SESSION['calendarSince']==$choseDate)
            {
                if(isset($_SESSION['firstTimeDate'])&&isset($_SESSION['calendarSince'])&&!isset($_SESSION['calendarUntill'])&&$_SESSION['calendarSince']==$choseDate)
                {
                    $_SESSION['calendarUntill'] = $choseDate;
                    return $_SESSION['calendarUntill'];
                }
                else
                {
                    $_SESSION['firstTimeDate']=false;
                }
            }

            // If the function checks all the conditions and still works, and if there is a calendar untill it returns the value
            if(isset($_SESSION['calendarUntill']))
            {
                return $_SESSION['calendarUntill'];
            }
        }
    }
}

