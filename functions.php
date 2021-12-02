<?php

/* Funkcja podająca nazwe miesiąca wpisująć liczbe w formacie "01" */
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

/* Funkcja dodająca zero na początku stringa jeżeli string ma 1 znak --> do poprawności daty */
function add_0_onBeginning($number)
{
    if(strlen($number)==1)
    {
        $number = '0'.$number;
    }
    return $number;
}

/* Funkcja zwracająca 1 -> wolny lub 0 -> zajęty, zależnie czy pokój jest zajęty */
function checkingIfRoomIsFree($room_id,$res_since,$res_untill)
{
    //Połączenie z bazą danych
    global $conn;

    //Sprawdzenie czy pokój w danych dniach nie ma żadnej rezerwacji
    $sqlCheckIfRoomIsFree = 'SELECT * FROM reservations,rooms
    WHERE
    (
        (
            "'.$res_since.'" = date_res_since AND
            "'.$res_since.'" = date_res_untill AND
            "'.$res_untill.'" = date_res_since AND
            "'.$res_untill.'" = date_res_untill
        )
        OR
        (
            "'.$res_since.'" >= date_res_since AND
            "'.$res_since.'" <= date_res_untill AND
            "'.$res_untill.'" >= date_res_since AND
            "'.$res_untill.'" >= date_res_untill
        )
        OR
        (
            "'.$res_since.'" <= date_res_since AND
            "'.$res_since.'" <= date_res_untill AND
            "'.$res_untill.'" >= date_res_since AND
            "'.$res_untill.'" <= date_res_untill
        )
        OR
        (
            "'.$res_since.'" >= date_res_since AND
            "'.$res_since.'" <= date_res_untill AND
            "'.$res_untill.'" >= date_res_since AND
            "'.$res_untill.'" <= date_res_untill
        )
        OR
        (
            "'.$res_since.'" = date_res_since AND
            "'.$res_since.'" <= date_res_untill AND
            "'.$res_untill.'" <= date_res_since AND
            "'.$res_untill.'" <= date_res_untill
        )
        OR
        (
            "'.$res_since.'" >= date_res_since AND
            "'.$res_since.'" <= date_res_untill AND
            "'.$res_untill.'" >= date_res_since AND
            "'.$res_untill.'" = date_res_untill
        )
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

/* Funkcja zwracająca 1 -> dla wystarczajaca duzego pokoju 0 -> za malego pokoju */  
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

/* Funkcja wypisujaca zmienna sesyjną o nazwie podanej w argumencie a nastepne unset */
function writeSessionValueByNameIfExists($ses_var_name)
{
    if(isset($_SESSION[$ses_var_name]))
    {
        $help = $_SESSION[$ses_var_name];
        unset($_SESSION[$ses_var_name]);
        return $help;
    }
}

/* Wyświetla wiersz wraz z odpowiednią zawartością gdy pokój w danym dniu jest wolny lub nie */
function dayInCalendar($day,$room_id,$date)
{
    //Na początku sprawdza czy podany dzień już się nie wydarzył
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
        //Pokój o podanej dacie jest wolny
        if(checkingIfRoomIsFree($room_id,$date,$date)==1)
        {
            echo'
                    <td>
                        <button name="choose-day" type="submit" value='.$day.' class="calendar-but free">'.$day.'</button>
                    </td>
                ';
        }
        //Pokój o podanej dacie jest zajęty
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

/* Wyświetlanie dat wybranych z kalendarza w formularzu */
function setDayInFormChoseFromCalendar($when,$year,$month)
{
    //Pole w formularzu SINCE - OD
    if($when=="since")
    {
        //Jeżeli kliknięto w jakąś date w kalendarzu
        if(isset($_POST['choose-day']))//Zmienna choose-day przechowuje date z kalendarza
        {
            //Przepisanie do zmiennej wybranej daty
            $choseDate = $year."-".$month."-".$_POST['choose-day'];

            /* Sprawdzenie czy już jakaś została wcześniej wybrana
               Zmienna sesyjna calendarSince przechowuje date od którego zaczynamy rezerwacje
               Zmienna sesyjna Calendar Untill przechowuje date kiedy kończymy rezerwacje */
            if(!isset($_SESSION['calendarSince'])&&!isset($_SESSION['calendarUntill']))
            {                  
                //do zmiennej przechowujacej date since przypisujemy wybrany dzień z kalendarza
                $_SESSION['calendarSince']=$choseDate;

                //wyświetlenie daty w formularzu
                return $_SESSION['calendarSince'];
            }

            //Jeżeli jest ustawione since i wybrana data jest wczesniej niz since
            if(isset($_SESSION['calendarSince'])&& $_SESSION['calendarSince']>$choseDate)
            {
                $_SESSION['calendarSince']=$choseDate;
                return $_SESSION['calendarSince'];
            }


            //Jeżeli funkcja sprawdzi wszystkie warunki i dalej działa, oraz jak istnieje calendar since to zwraca wartosc
            if(isset($_SESSION['calendarSince']))
            {
                return $_SESSION['calendarSince'];
            }

        }
    }



    //Pole w formularzu UNTILL - DO
    if($when=="untill")
    {
        if(isset($_POST['choose-day']))
        {
            $choseDate = $year."-".$month."-".$_POST['choose-day'];
            //Jeżeli ustawiony jest początek, lecz koniec nie oraz wybrana data jest później niz ustawiony poczatek
            if(isset($_SESSION['calendarSince'])&&$choseDate>$_SESSION['calendarSince']&&!isset($_SESSION['calendarUntill']))
            {
                $_SESSION['calendarUntill']=$choseDate;
                return $choseDate;
            }

            //Jeżeli jest ustawione since i wybrana data jest wczesniej niz since TO ZABEZPIECZENIE zeby untill tez sie nie zmienial
            if(isset($_SESSION['calendarSince'])&& $_SESSION['calendarSince']>$choseDate && isset($_SESSION['calendarUntill']))
            {
                return $_SESSION['calendarUntill'];
            }


            //Jeżeli ustawiony jest since oraz ustawiony jest untill i wybrana jest data większa od since
            if(isset($_SESSION['calendarSince'])&&isset($_SESSION['calendarUntill'])&&$choseDate>$_SESSION['calendarSince'])
            {
                $_SESSION['calendarUntill']=$choseDate;
                return $_SESSION['calendarUntill'];
            }

            //Jeżeli ustawiony jest poczatek ale nie koniec oraz podano ta samą date
            //+ zabezpieczenie zeby od razu nie ustawialo takiej samej daty jak w since
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

            //Jeżeli funkcja sprawdzi wszystkie warunki i dalej działa, oraz jak istnieje calendar untill to zwraca wartosc
            if(isset($_SESSION['calendarUntill']))
            {
                return $_SESSION['calendarUntill'];
            }
        }
    }
}

