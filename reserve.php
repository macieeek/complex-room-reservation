<?php
    session_start();

    /* Initializing database connection */
    include_once("db-connect.php");

    /* Helpers */
    include_once("functions.php");

    /* Clearing the booking form after pressing the clear button */
    if(isset($_POST['clear-form']))
    {
        if(isset($_SESSION['phone-number']))unset($_SESSION['phone-number']);
        if(isset($_SESSION['name']))unset($_SESSION['name']);
        if(isset($_SESSION['surname']))unset($_SESSION['surname']);
        if(isset($_SESSION['how-many-people']))unset($_SESSION['how-many-people']);
        if(isset($_SESSION['calendarSince']))unset($_SESSION['calendarSince']);
        if(isset($_SESSION['calendarUntill']))unset($_SESSION['calendarUntill']);
    }

    /* Reservation form handling */
    if(isset($_POST['reserve-room']))
    {   
        // Checking if the form has been submitted fully
        if(empty($_POST['room'])||empty($_POST['res-since'])||
           empty($_POST['res-untill'])||empty($_POST['phone-number'])||
           empty($_POST['name'])||empty($_POST['surname'])||
           empty($_POST['how-many-people']))
        {
            if(!empty($_POST['room']))$_SESSION['room']=$_POST['room'];
            if(!empty($_POST['res-since']))$_SESSION['res-since']=$_POST['res-since'];
            if(!empty($_POST['res-untill']))$_SESSION['res-untill']=$_POST['res-untill'];
            if(!empty($_POST['phone-number']))$_SESSION['phone-number']=$_POST['phone-number'];
            if(!empty($_POST['name']))$_SESSION['name']=$_POST['name'];
            if(!empty($_POST['surname']))$_SESSION['surname']=$_POST['surname'];
            if(!empty($_POST['how-many-people']))$_SESSION['how-many-people']=$_POST['how-many-people'];

            $_SESSION['choose-room']=true;

            header("Location: reserve.php");
            exit();
        }

        /* If the start date of the reservation is later than the end */
        /* and if the start date is a day that has already passed */
        if($_POST['res-since']>$_POST['res-untill'] || date("Y-m-d") > $_POST['res-since'])
        {
            $_SESSION['reservation-error']=true;
        }
        else
        {
            // assigning posts to variables
            $room =          $_POST['room'];
            $reserveSince =  $_POST['res-since'];
            $reserveUntill = $_POST['res-untill'];
            $phoneNumber =   $_POST['phone-number'];
            $name =          $_POST['name'];
            $surname =       $_POST['surname'];
            $howManyPeople = $_POST['how-many-people'];

            /* If the room is empty and is large enough */
            if(checkingIfRoomIsFree($room,$reserveSince,$reserveUntill)==1
               && checkingPeopleInRoom($room,$howManyPeople)==1)
            {
                // Date on which the reservation will be made
                $dateReservation = date("Y-m-d");
    
                // Insert reservation
                $sqlInsertReservation = 'INSERT INTO reservations
                (date_reservation, date_res_since, date_res_untill, room_id,
                phone_number, name, surname)
                VALUES
                ("'.$dateReservation.'","'.$reserveSince.'","'.$reserveUntill.'",
                '.$room.',"'.$phoneNumber.'","'.$name.'","'.$surname.'")';
                
                if($conn->query($sqlInsertReservation)===TRUE)
                {
                    // Create session variables to display a message
                    $_SESSION['reservation-completed']=true;
                    $_SESSION['room']=$room;
                    $_SESSION['reserveSince']=$reserveSince;
                    $_SESSION['reserveUntill']=$reserveUntill;
                }
            }
            /* Something went wrong so an error */
            else
            {
                $_SESSION['reservation-error']=true;
            }
        }
    }

    /* If day is selected, set the session variable with room */
    if(isset($_POST['choose-room']))
    {
        $_SESSION['choose-room']=$_POST['choose-room'];
    }

?>

<!DOCTYPE html>
<html lang="pl">

<head>

    <title>Rezerwacja pokoju</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="styles/index.css">
    <link rel="stylesheet" type="text/css" href="styles/nav.css">
    <link rel="stylesheet" type="text/css" href="styles/reserve.css">
    
</head>

<body>
<div id="container">

    <!-- NAV -->
    <div id="nav">

        <?php include_once("nav.php"); ?>

    </div>
    
    <!-- CONTENT -->
    <div id="content">


        <!-- title -->
        <div id="content-title">
            Wybierz pokój do rezerwacji
        </div>

        <!-- Hotel map - selecting a room -->
        <div id="table-div">
             <table id="rooms-table">

                <tr>
                    <td colspan="8" style="background-color: rgb(126, 101, 55);">Plaża</td>
                </tr>

                <form action="#" method="POST">
                    <tr>
                        <td><button name="choose-room" value="1" class="room-but"><span class="id-room">1</span><br> 1 os.</button></td>
                        <td><button name="choose-room" value="2" class="room-but"><span class="id-room">2</span><br> 1 os.</button></td>
                        <td><button name="choose-room" value="3" class="room-but"><span class="id-room">3</span><br> 1 os.</button></td>
                        <td><button name="choose-room" value="4" class="room-but"><span class="id-room">4</span><br> 2 os.</button></td>
                        <td><button name="choose-room" value="5" class="room-but"><span class="id-room">5</span><br> 2 os.</button></td>
                        <td><button name="choose-room" value="6" class="room-but"><span class="id-room">6</span><br> 2 os.</button></td>
                        <td><button name="choose-room" value="7" class="room-but"><span class="id-room">7</span><br> 2 os.</button></td>
                        <td><button name="choose-room" value="8" class="room-but"><span class="id-room">8</span><br> 2 os.</button></td>
                    </tr>

                    <tr>
                        <td colspan="8" style="background-color: orange;">Hol</td>
                    </tr>

                    <tr>
                        <td><button name="choose-room" value="9" class="room-but"><span class="id-room">9</span><br> 3 os.</button></td>
                        <td><button name="choose-room" value="10" class="room-but"><span class="id-room">10</span><br> 3 os.</button></td>
                        <td><button name="choose-room" value="11" class="room-but"><span class="id-room">11</span><br> 3 os.</button></td>
                        <td><button name="choose-room" value="12" class="room-but"><span class="id-room">12</span><br> 3 os.</button></td>
                        <td><button name="choose-room" value="13" class="room-but"><span class="id-room">13</span><br> 3 os.</button></td>
                        <td><button name="choose-room" value="14" class="room-but"><span class="id-room">14</span><br> 3 os.</button></td>
                        <td><button name="choose-room" value="15" class="room-but"><span class="id-room">15</span><br> 4 os.</button></td>
                        <td><button name="choose-room" value="16" class="room-but"><span class="id-room">16</span><br> 4 os.</button></td>
                    </tr>
                </form>

                <tr>
                    <td colspan="8" style="background-color: green;">Las</td>
                </tr>

            </table>
        </div>
        


    <?php
        /* Selected room */
        /* Shows calendar and reservation form */
        if(isset($_POST['choose-room'])||isset($_SESSION['choose-room']))// choose-room -> room number 
        {
            // If session variable choose-room doesnt exist -> creating session variable choose-room
            if(!isset($_SESSION['choose-room'])) $_SESSION['choose-room']=$_POST['choose-room'];

            // If a session variable exists and another room is selected, we change the value of the variable to the currently selected one
            if(isset($_SESSION['choose-room'])&&isset($_POST['choose-room']))$_SESSION['choose-room']=$_POST['choose-room'];

            /* Setting month if it hasnt been set before */
            if(!isset($_SESSION['month']))
            {
                // date('m') returns the value of the current month in the form "03"
                $monthString = nameOfMonth(date('m'));
            }
            /* If already selected a month and click on chane again */
            if(isset($_SESSION['month'])&&isset($_POST['changeMonth'])&&isset($_SESSION['changedAtLeastOnce']))
            {
                // select a month greater than 12 then +1 year and it gives January
                if($_SESSION['month']>=12 && (int)$_POST['changeMonth']==1)
                {
                    if(!isset($_SESSION['year'])) $_SESSION['year']=(int)date('Y');
                    if(isset($_SESSION['year'])) $_SESSION['year'] += 1;
                    $_SESSION['month']=0;
                }
                // Month was moved back and the currently selected month is January, move the year back by one and it is December
                if($_SESSION['month']==1 && (int)$_POST['changeMonth']==(-1) )
                {
                    if(!isset($_SESSION['year'])) $_SESSION['year']=(int)date('Y');
                    if(isset($_SESSION['year'])) $_SESSION['year'] -= 1;
                    $_SESSION['month']=13;
                }
                $nrCurrentMonth = $_SESSION['month'];
                $valueChangeMonth = (int)$_POST['changeMonth'];

                $_SESSION['month'] = $nrCurrentMonth + $valueChangeMonth;

                $monthString = nameOfMonth($_SESSION['month']);
            }
            /* Changed month for the first time */
            if(!isset($_SESSION['month'])&&isset($_POST['changeMonth']))
            {
                $nrCurrentMonth = (int)date('m');
                $valueChangeMonth = (int)$_POST['changeMonth'];

                // value of changed month INT
                $_SESSION['month'] = $nrCurrentMonth + $valueChangeMonth;

                // assignment to the month string
                $monthString = nameOfMonth($_SESSION['month']);

                $_SESSION['changedAtLeastOnce']=true;
            }
            
            
            /* Set year */
            if(!isset($_SESSION['year']))$year = date('Y');
            if(isset($_SESSION['year']))$year=$_SESSION['year'];

            /* Set name of the month */
            if(!isset($_SESSION['month']))$monthString = nameOfMonth(date('m'));
            if(isset($_SESSION['month']))$monthString = nameOfMonth($_SESSION['month']);


            /* Display a div with today's date and a message about the colors in the calendar */
            echo'
                <div id="todays-date">
                
                    Dzisiejsza data: <b>'.date("d.m.Y").'</b>
                    
                    <div style="font-size:16px;">

                        <br>
                        
                        <span class="green-font">Zielony kolor</span>
                        
                        w poniższym kalendarzu oznacza to, że 
                        
                        <br>pokój w tym dniu jest 
                        
                        <span class="green-font">wolny</span>, natomiast 
                        
                        <span class="red-font">czerwony, że jest zajęty.</span>

                    </div>

                </div>';


            /* Calendar */
            echo'
                <div id="calendar-div">

                    <table id="calendar-table">
                        
                        <tr>
                            <th colspan="7">Kalendarz<br>'.$monthString.' '.$year.'
                            <br>Pokój:'.$_SESSION['choose-room'].'</th>
                        </tr>

                        <tr>
                            <td><b> pon </b></td>
                            <td><b> wt  </b></td>
                            <td><b> śr  </b></td>
                            <td><b> czw </b></td>
                            <td><b> pią </b></td>
                            <td><b> sob </b></td>
                            <td><b> nie </b></td>
                        </tr>

                    <!-- Wyświetlanie dni w kalendarzu -->
                    <form action="#" method="POST">';

                    /* Display the days in the calendar for the current month when the date hasnt changed */
                    if(!isset($_SESSION['month']))
                    {
                        // Returns the number of days in the current month
                        $howManyDays = date('t');

                        // Calendar day counter
                        $counterDays = 1;
                        $day = &$counterDays;

                        $month = date('m');

                        $currentMonthYearDate = $year.'-'.$month.'-01';

                        // Create an object on a given date
                        $date = new DateTime($currentMonthYearDate);

                        // Mechanism of displaying a given number of days how many there are in a given month
                        while($howManyDays>=0)
                        {
                            echo'<tr>';

                            // Every 7 iterations <tr></tr> to go to next row in table
                            for($i = 1; $i <= 7; $i++)
                            {
                                // If the day has already exceeded the number of days in the month --> break;
                                if($day>date('t'))
                                {
                                    break;
                                }
                                // Convert from object to string to display function <td></td>
                                $date_string = $date->format('Y-m-d');
                                dayInCalendar($day,$_SESSION['choose-room'],$date_string);

                                // Add +1 day to the date in the object
                                $date->add(new DateInterval('P1D'));
                                $day++;
                                $howManyDays--;
                            }

                            echo'</tr>';
                            
                            // If the day has already exceeded the number of days in the month --> break;
                            if($day>date('t'))
                            {
                                break;
                            }
                        }
                    }
                    /*  Display the days in the calendar for the current month when the date changes */
                    if(isset($_SESSION['month']))
                    {
                        $howManyDays = cal_days_in_month(CAL_GREGORIAN,(int)$_SESSION['month'],(int)$year);
                        $howManyDaysInMonthHELP = $howManyDays;
                        
                        $counterDays = 1;
                        $day = &$counterDays;

                        $ChoosenMonthYearDate = $year.'-'.$_SESSION['month'].'-01';

                        $month = $_SESSION['month'];

                        // To make the month in the format "0x" 
                        $month = add_0_onBeginning($month);

                        $date = new DateTime($ChoosenMonthYearDate);

                        while($howManyDays>=0)
                        {
                            echo'<tr>';

                            for($i = 1; $i <=7; $i++)
                            {
                                if($day>$howManyDaysInMonthHELP)
                                {
                                    break;
                                }
                                $date_string = $date->format("Y-m-d");
                                dayInCalendar(add_0_onBeginning($day),$_SESSION['choose-room'],$date_string);

                                $date->add(new DateInterval('P1D'));
                                $day++;
                                $howManyDays--;
                            }

                            echo'</tr>';

                            if($day>$howManyDaysInMonthHELP)
                            {
                                break;
                            }
                        }  
                    }


            echo'
                    </form>
                    </table>
                    
                    <!-- STRZAŁKI DO PRZEWIJANIA MIESIĘCY USTAWIONE ABSOLUTE -->

                    <form action="#" method="POST">
                        <button id="right-arrow" type="submit" name="changeMonth" value="1"> --> </button>
                        <button id="left-arrow"  type="submit" name="changeMonth" value="-1"> <-- </button>
                    </form>

                 </div>
                ';


                        
            echo'
                <div id="choose-date-title">
                    <b>Formularz rezerwacji:</b>
                </div>

                <div id="choose-date-div">

                    <form action="#" method="POST">

                        <div id="choose-date-labels">

                            <label for="room">Pokój:</label><br>

                            <label for="res-since">Rezerwacja od dnia:</label><br>

                            <label for="res-untill">Rezerwacja do dnia:</label><br>

                            <label for="phone-number">Numer telefonu:</label><br>

                            <label for="name">Imię:</label><br>

                            <label for="surname">Nazwisko:</label><br>

                            <label for="how-many-people">Ile osób:</label>

                        </div>

                        <div id="choose-date-inputs">

                            <input type="text" readonly="readonly" name="room" value='.$_SESSION['choose-room'].' style="width: 48px;" ><br>

                            <input type="date" name="res-since" value='.setDayInFormChoseFromCalendar("since",$year,$month).'><br>

                            <input type="date" name="res-untill" value='.setDayInFormChoseFromCalendar("untill",$year,$month).'><br>

                            <input type="text" name="phone-number" value='.writeSessionValueByNameIfExists('phone-number').'><br>

                            <input type="text" name="name" value='.writeSessionValueByNameIfExists('name').'><br>

                            <input type="text" name="surname" value='.writeSessionValueByNameIfExists('surname').'><br>

                            <input type="number" min="1" max="4" name="how-many-people" value='.writeSessionValueByNameIfExists('how-many-people').'><br>
                        
                        </div>

                        <div id="reserve-submit-div">
                        
                            <button id="reserve-button" name="reserve-room" type="submit" >Zarezerwuj pokój</button>
                        
                        </div>';

                        /* If the reservation was successful it displays a message: */
                        if(isset($_SESSION['reservation-completed']))
                        {
                            echo'
                                    <div id="reserve-information-div">
                                        Pomyślnie zarezerwowano pokój <b>'.$_SESSION['room'].'</b><br>
                                        od <b>'.$_SESSION['reserveSince'].'</b>  do  <b>'.$reserveUntill.'</b>
                                    </div>
                                ';

                            unset($_SESSION['reservation-completed']);
                            unset($_SESSION['room']);
                            unset($_SESSION['reserveSince']);
                            unset($_SESSION['reserveUntill']);
                        }

                        /* If the reservation is unsuccessful it displays a message: */
                        if(isset($_SESSION['reservation-error']))
                        {
                            echo'
                                    <div id="reserve-information-div">
                                        Wystąpił błąd podczas rezerwacji.
                                    </div>
                                ';
                            unset($_SESSION['reservation-error']);
                        }

                        /* Cleanup button */
                    echo'
                        <form action="#" method="POST">
                            <button id="clear-form-button" type="submit" value="clear" name="clear-form">Wyczyść formularz</button>
                        </form>
                    </form>
                </div>
                        ';
                    
        }
    ?>

        
    </div>

<?php $conn->close(); ?>
</div>

</body>
</html>