<?php 

class Excel_export_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->library('datatables');
    }

    public function exportLocationWise($dateStart, $dateEnd) {
      $sql = "SELECT 
      job.`location` AS location,
      SUM(job.`total_bill`) AS total_sale,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.cash ELSE 0 END) - SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.cash ELSE 0 END) /105 * 5, 3)AS cash,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.cash ELSE 0 END) / 105 * 5, 3) AS cash_vat,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.credit ELSE 0 END) - SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.credit ELSE 0 END) / 105 * 5, 3) AS credit,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.credit ELSE 0 END) / 105 * 5, 3) AS credit_vat,
      SUM(job.`parkpass`) AS parkpass,
      SUM(job.`points`) AS points,
      SUM(CASE WHEN job.`payment_type` = 'Exception' AND job.`total_bill` = job.`payed_amount` THEN job.`total_bill` ELSE 0 END) AS paid_exception,
      SUM(CASE WHEN job.`payment_type` = 'Exception' AND job.`total_bill` != job.`payed_amount` THEN job.total_bill ELSE 0 END) AS not_paid_exception
      FROM job WHERE location <> 'Events' AND location <> 'Test1' AND location <> 'Test2' AND job.`handover_time` BETWEEN '$dateStart 06:00:00' AND '$dateEnd 06:00:00' GROUP BY job.`location`;";

      $return['records'] = $this->db->query($sql);

      $sqlTotal = "SELECT
      job.`location` AS location,
      SUM(job.`total_bill`) AS total_sale,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.cash ELSE 0 END) - SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.cash ELSE 0 END) /105 * 5, 3) AS cash,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.cash ELSE 0 END) / 105 * 5, 3) AS cash_vat,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.credit ELSE 0 END) - SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.credit ELSE 0 END) / 105 * 5, 3) AS credit,
      ROUND(SUM(CASE WHEN job.`payment_type` != 'Exception' THEN job.credit ELSE 0 END) / 105 * 5, 3) AS credit_vat,
      SUM(job.`parkpass`) AS parkpass,
      SUM(job.`points`) AS points,
      SUM(CASE WHEN job.`payment_type` = 'Exception' AND job.`total_bill` = job.`payed_amount` THEN job.`total_bill` ELSE 0 END) AS paid_exception,
      SUM(CASE WHEN job.`payment_type` = 'Exception' AND job.`total_bill` != job.`payed_amount` THEN job.total_bill ELSE 0 END) AS not_paid_exception
      FROM job WHERE location <> 'Events' AND location <> 'Test1' AND location <> 'Test2' AND job.`handover_time` BETWEEN '$dateStart 06:00:00' AND '$dateEnd 06:00:00';";

      $return['total'] = $this->db->query($sqlTotal);

      return $return;

    }

    public function exportUserWise($date, $location, $gate) {

      $dateArray = explode('-', $date);
      $dateStart = date('Y-m-d 06:00:00', strtotime($dateArray[0]));
      $dateEnd   = date('Y-m-d 06:00:00', strtotime($dateArray[1]));

      // echo $dateStart;
      // echo '<br>';
      // echo $dateEnd;

      $sql = "SELECT 
      job_history.`attendant_id`,
      attendant.`name`,
      SUM(job.`total_bill`) AS TotalAmount,
      SUM(job.`cash`) AS Cash,
      SUM(job.`credit`) AS Credit,
      SUM(job.`parkpass`) AS ParkPass,
      SUM(job.`discount`) AS Discount,
      SUM(
        CASE
          WHEN job.`payment_type` LIKE '%Exception%'
          THEN job.`total_bill`
          ELSE 0
        END
      ) AS Exception,
     (SELECT SUM(job.`total_bill`) FROM job WHERE job.location = '$location'
      AND job.handover_time BETWEEN '$dateStart'
      AND '$dateEnd'
      AND job.`phase` = 7
      AND job.entry_gate = '$gate'
      AND job_history.`phase` = 7
    )AS GrandTotal
    FROM
      job_history
      INNER JOIN job
        ON job.`id` = job_history.`job_id`
      INNER JOIN attendant
        ON attendant.`id` = job_history.`attendant_id`
    WHERE job.location = '$location'
      AND job.handover_time BETWEEN '$dateStart'
      AND '$dateEnd'
      AND job.`phase` = 7
      AND job.entry_gate = '$gate' 
      AND job_history.`phase` = 7 
    GROUP BY job_history.`attendant_id`;";

    $return = $this->db->query($sql);

        return $return;

    }

    public function exception($date, $ticket, $location, $days, $car, $gate) {
      
      $this->db->select('tbl_exception.`tex_date` AS Exception_Date,
                  job.`location` AS Location,
                  job.id AS Ticket,
                  job.`car_plate_number` AS Vehicle_no,
                  CONCAT(job.`receive_time`,",",job.`handover_time`) AS Transaction_time,
                  job.`total_bill` AS Total_Bill,
                  
                  (SELECT SUM(tbl_exception_update.`teu_paidamt`) FROM tbl_exception_update WHERE tbl_exception_update.`teu_ticket`=job.`id`) AS Paid_Amount, 
                  (SELECT Total_Bill-Paid_Amount) AS Exception_Amount,
                  
                  tbl_exception.`tex_remark` AS Remark,
                  tbl_exception.`tex_remarkby` AS Prepared_by,
                  `tbl_exception`.`tex_justification` AS Justiyfy_by,
                  (SELECT tbl_admin_user.`tau_uname` FROM tbl_admin_user WHERE tbl_admin_user.`tau_id`=`tbl_exception`.`tex_aproveby`) AS Approved_by');
        $this->db->from('tbl_exception');
        $this->db->join('job','tbl_exception.tex_ticket=job.id');

         if($date) {
            $dateSplitInHalf = explode('-', $date); //array
            $dateStart       = date('Y-m-d H:i:s', strtotime($dateSplitInHalf[0]));//string
            $dateEnd         = date('Y-m-d H:i:s', strtotime($dateSplitInHalf[1]));//string
            
            $this->db->where('tex_timeout >=', $dateStart);
            $this->db->where('tex_timeout <=', $dateEnd);
         }

         if($location) {
            $this->db->where_in('job.location', $location);
         }

         if($gate) {
            $this->db->where('job.exit_gate', $gate);
         }

         if($ticket) {
            $this->db->where('tbl_exception.tex_ticket', $ticket);
         }

         if($days) {
            $daysOfWeek = "'".implode( "', '", $days)."'";  //implode it to a string
            $where = "DAYNAME(tbl_exception.tex_timeout) IN ({$daysOfWeek})";
            $this->db->where($where);
         }

         $exception = $this->db->get();

      return $exception;  
    }

    public function dailySalesDetails($dayss, $date, $days, $acctype, $location, $car, $ticket, $tictype, $service) {
      $this->db->select('
      `job`.`id` AS `ticket`,
      `job`.`location` AS `site`,
      `job`.`car_plate_number` AS `vehicleno`,
       `account_types`.`account_types` AS `membership`,
      (SELECT 
        car.Make 
      FROM
        car 
      WHERE job.car_plate_number = car.car_plate_number 
      LIMIT 1) AS `make`,
      DATE_FORMAT(
        `job`.`receive_time`,
        "%Y-%m-%d %H:%i"
      ) AS `timein`,
      (
        CASE
          WHEN `job`.`handover_time` != "" 
          THEN DATE_FORMAT(
            `job`.`handover_time`,
            "%Y-%m-%d %H:%i"
          ) 
          ELSE 0 
        END
      ) AS `timeout`,
      TIMEDIFF(
        job.handover_time,
        job.receive_time
      ) AS totaltime,
      `job`.`payed_amount` AS `amount`,
      (
        CASE
          WHEN job.`payment_type` != "Exception" 
          THEN job.`cash` - ROUND((job.`cash`/105) * 5, 3) 
          ELSE 0 
        END
      ) AS cash,
      (
        CASE
          WHEN job.`payment_type` != "Exception" 
          THEN  ROUND((job.`cash`/105) * 5, 3)
          ELSE 0 
        END
      ) AS vat_cash,
      (
        CASE
          WHEN job.`payment_type` != "Exception" 
          THEN ROUND((job.`credit`/105) * 5, 3) 
          ELSE 0 
        END
      ) AS vat_credit,
      (
        CASE
          WHEN job.`payment_type` != "Exception" 
          THEN job.`credit` - ROUND((job.`credit`/105) * 5, 3)
          ELSE 0 
        END
      ) AS credit,
      `job`.`points` AS points,
      `job`.`parkpass` AS parkpass,
	  (CASE WHEN job.validation LIKE "%true%" THEN "Yes" ELSE "No" END) AS validation,
      (
        CASE
          WHEN `job`.`payment_type` = "Exception" 
          THEN `job`.`payed_amount` 
          ELSE 0 
        END
      ) AS exception,

      (CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND((CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_exception,
      ROUND((CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_vat_exception,
      (CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
      ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS vat_credit_exception,
   

      (
        CASE
          WHEN `job`.`payment_type` = "Exception" 
          THEN `job`.`total_bill` - `job`.`payed_amount` 
          ELSE 0 
        END
      ) AS exception_notpaid,
      (
        FORMAT(
          (
            job.`total_bill` * (job.`discount`) / 100
          ),
          3
        )
      ) AS discount,
      (SELECT 
        `attendant`.`name` 
      FROM
        `job_history` 
        INNER JOIN `attendant` 
          ON `attendant`.`id` = `job_history`.`attendant_id` 
      WHERE job.`id` = `job_history`.`job_id` 
        AND `job_history`.`phase` IN (1, 2, 3) 
      LIMIT 1) AS att1,
      (SELECT 
        `attendant`.`name` 
      FROM
        `job_history` 
        INNER JOIN `attendant` 
          ON `attendant`.`id` = `job_history`.`attendant_id` 
      WHERE job.`id` = `job_history`.`job_id` 
        AND `job_history`.`phase` IN (7, 6) 
      LIMIT 1) AS att2,
      (job.entry_gate) AS p1,
      (job.exit_gate) AS p2');

      $this->db->from('job');
      $this->db->join('account', 'account.`id`=job.`account_id`', 'INNER');
      $this->db->join('account_types', 'account_types.`id` = account.`account_types_id`', 'INNER');

      if($date)
        {
           $val=$date;
           $dates = explode('-',$val);
           $days="";  
           
          if(trim($dates[1])==trim($dates[0]))
           {
             $where=" job.`receive_time` between '".date('Y-m-d H:i:s A',strtotime($dates[0]))."' and '".date('Y-m-d',strtotime('+1 day',strtotime($dates[0])))."'";
             $this->db->where($where);
           }
           else
           {
              $temp=false;
              if($days)
              {
                $x=$days;
                $temp="'" . implode("','", $x) . "'";  
              }
              
              if($temp)
              {
                $dayss=" AND DAYNAME(job.`receive_time`) IN (".$temp.")";
              }
              
              $where='job.receive_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($dates[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($dates[1])).'"'.$dayss;
              $this->db->where($where); 
           }
          
        }
        if($days)
        {
           $x=$days;
           $temp="'" . implode("','", $x) . "'"; 
           $where=" DAYNAME(job.`receive_time`) IN (".$temp.")";  
           $this->db->where($where);
        } 
        if($acctype)
        {
           $val=$acctype; 
           $this->db->where('account_types.id',$val); 
        }
        if($location)
        {
           $x=$location;
           $temp="'" . implode("','", $x) . "'"; 
           $where=" job.location IN (".$temp.")";
           $this->db->where($where);    
        }
        if($car)
        {
           $x=$car;
           $temp="'" . implode("','", $x) . "'"; 
           $where=" car.Make IN (".$temp.")";
           $this->db->where($where);    
        }
        if($ticket)
        {
           $val=$ticket;
           $this->db->where('job.id',$val);  
            
        }
        if($tictype)
        {
          $val=$tictype;
          $this->db->where('job.service_type',$val);  
        }
        if($service) {
          $this->db->where('job.service_type', $service);
        }
    
      $dailySalesDetails['records'] = $this->db->get();

      //get total of the fields in job table
      $this->db->select('
		SUM(ROUND (job.`payed_amount`,3)) AS total,
		SUM(CASE WHEN job.`payment_type` != "Exception" THEN job.`cash` - ROUND((job.`cash`/105) * 5, 3)ELSE 0 END) AS cash,		
		SUM(CASE WHEN job.`payment_type` != "Exception" THEN  ROUND((job.`cash`/105) * 5, 3)ELSE 0 END) AS vat_cash,
		SUM(CASE WHEN job.`payment_type` != "Exception" THEN job.`credit` - ROUND((job.`credit`/105) * 5, 3)ELSE 0 END) AS credit,
		SUM(CASE WHEN job.`payment_type` != "Exception" THEN ROUND((job.`credit`/105) * 5, 3) ELSE 0 END ) AS vat_credit,
        SUM(`job`.`points`) AS `points`,
        SUM(parkpass) AS parkpass,
        ROUND(SUM(
          CASE
            WHEN job.`payment_type` = "Exception" 
            THEN job.`payed_amount` 
            ELSE 0 
          END
        ),3) AS exception,

        SUM((CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END,3)) AS cash_exception,
        SUM(ROUND((CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3)) AS cash_vat_exception,
		SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
		SUM(ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3)) AS vat_credit_exception,
     

        ROUND(SUM(
          CASE
            WHEN job.`payment_type` = "Exception" 
            THEN job.`total_bill` - job.`payed_amount` 
            ELSE 0 
          END
        ),3) AS exception_notpaid
      ');

      $this->db->from('job');
      $this->db->join('account', 'account.`id`=job.`account_id`', 'INNER');
      $this->db->join('account_types', 'account_types.`id` = account.`account_types_id`', 'INNER');

      if($date)
      {
         $val=$date;
         $dates = explode('-',$val);
         $days="";  
         
        if(trim($dates[1])==trim($dates[0]))
         {
           $where=" job.`handover_time` between '".date('Y-m-d',strtotime($dates[0]))."' and '".date('Y-m-d',strtotime('+1 day',strtotime($dates[0])))."'";
           $this->db->where($where);
         }
         else
         {
            $temp=false;
            if($days)
            {
              $x=$days;
              $temp="'" . implode("','", $x) . "'";  
            }
            
            if($temp)
            {
              $dayss=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
            }
            
            $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($dates[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($dates[1])).'"'.$dayss;
            $this->db->where($where); 
         }
        
      }
      if($days)
      {
         $x=$days;
         $temp="'" . implode("','", $x) . "'"; 
         $where=" DAYNAME(job.`handover_time`) IN (".$temp.")";  
         $this->db->where($where);
      } 
      if($acctype)
      {
         $val=$acctype; 
         $this->db->where('account_types.id',$val); 
      }
      if($location)
      {
         $x=$location;
         $temp="'" . implode("','", $x) . "'"; 
         $where=" job.location IN (".$temp.")";
         $this->db->where($where);    
      }
      if($car)
      {
         $x=$car;
         $temp="'" . implode("','", $x) . "'"; 
         $where=" car.Make IN (".$temp.")";
         $this->db->where($where);    
      }
      if($ticket)
      {
         $val=$ticket;
         $this->db->where('job.id',$val);  
          
      }
      if($tictype)
      {
        $val=$tictype;
        $this->db->where('job.service_type',$val);  
      }
      if($service) {
        $this->db->where('job.service_type', $service);
      }

      $dailySalesDetails['total'] = $this->db->get();


      return $dailySalesDetails;
    }

    public function dailySales($dates, $days, $acctype, $location, $car, $staff, $gate, $service,$client) {
      //gets records from job table
      $this->db->select('
      `job`.`id` AS `ticket`,
      `job`.`location` AS `site`,
      `job`.`car_plate_number` AS `vehicleno`,
      (CASE WHEN job.`payment_type`!="Exception" THEN job.`cash` - ROUND((job.`cash`/105) * 5, 3) ELSE 0 END) AS cash,
      (CASE WHEN job.`payment_type`!="Exception" THEN ROUND((job.`cash`/105) * 5, 3) ELSE 0 END) AS vat_cash,
      (CASE WHEN job.`payment_type`!="Exception" THEN job.`credit` - ROUND((job.`credit`/105) * 5, 3) ELSE 0 END) AS credit,
      (CASE WHEN job.`payment_type`!="Exception" THEN ROUND((job.`credit`/105) * 5, 3) ELSE 0 END) AS vat_credit,
      `job`.`points` AS `points`,
      `job`.`parkpass` AS `parkpass`,

      (CASE WHEN at.id = 1 AND cl.id = 2 THEN at.account_types ELSE cl.name END) AS client_name,

      (CASE  
      WHEN  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) < pcr.from  AND  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) <= pcr.to THEN pcr.rate 
      WHEN TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) > pcr.from  AND  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) <= pcr.to THEN pcr.rate 
      ELSE 0
      END )AS client_amount,
	 	
      (CASE WHEN parkpass LIKE "%yes%" THEN (job.total_bill- job.payed_amount) ELSE 0 END ) AS parkpass_amount,
    (CASE WHEN validation LIKE "%true%" THEN (job.total_bill- job.payed_amount) ELSE 0 END) AS validation_discount, 
    (CASE WHEN job.validation LIKE "%true%" THEN "Yes" ELSE "No" END) AS validation,
                
      (CASE WHEN `job`.`payment_type` = "Exception" THEN `job`.`total_bill` ELSE 0 END) AS exception,
      (CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND((CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_exception,
      ROUND((CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_vat_exception,
      (CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
      ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS vat_credit_exception,

      

      (CASE WHEN `job`.`payment_type` = "Exception" THEN `job`.`total_bill` - `job`.`payed_amount` ELSE 0 END) AS exception_notpaid,
      (select car.Make from car where job.car_plate_number=car.car_plate_number limit 1) AS `make`,
      job.`exit_gate` as gate,
      DATE_FORMAT(`job`.`receive_time`,"%Y-%m-%d %H:%i") AS `timein`,
      FORMAT(job.payed_amount,3) AS `amount`,
      (case when `job`.`handover_time`!="" then DATE_FORMAT(`job`.`handover_time`,"%Y-%m-%d %H:%i") else 0 end) AS `timeout`
      ');

      $this->db->from('job');
      $this->db->join('account a','a.id = job.account_id');
      $this->db->join('account_types at','at.id = a.account_types_id');
      $this->db->join('customers c','c.id = a.id');
      $this->db->join('client cl','cl.id = c.client_id');
      $this->db->join('pass_client_rates pcr','pcr.client_id = cl.id AND pcr.account_type = at.id','LEFT'); 	


      if($dates)
      {
         $val=$dates;
         $date = explode('-',$val);
         $days="";  
        if(trim($date[1])==trim($date[0]))
         {
           $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime('+1 day',strtotime($date[0]))).'"'.$days;
           $this->db->where($where);
         }
         else
         {
            $temp=false;
            if($days)
            {
              $x=$days;
              $temp="'" . implode("','", $x) . "'";  
            }
            
            if($temp)
            {
              $days=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
            }
            
            $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$days;
            $this->db->where($where); 
         }
        
      }
      if($days)
      {
         $x=$days;
         $temp="'" . implode("','", $x) . "'"; 
         $where=" DAYNAME(job.`handover_time`) IN (".$temp.")";  
         $this->db->where($where);
      } 
      if($acctype)
      {
         $val=$acctype; 
         $this->db->where('account_types.id',$val); 
      }
      if($location)
      {
         $x=$location;
         $temp="'" . implode("','", $x) . "'"; 
         $where=" job.location IN (".$temp.")";
         $this->db->where($where);    
      }
      if($car)
      {
         $x=$car;
         $temp="'" . implode("','", $x) . "'"; 
         $where=" car.Make IN (".$temp.")";
         $this->db->where($where);    
      }
      if($staff)
      {
         $val=$staff;
         $where=" job.id IN(".$temp2.")";
         $this->db->where($where);  
      }

      if($gate) {
        $this->db->where('exit_gate', $gate);
      }
      if($service) {
        $this->db->where('service_type', $service);
      }
      if($client) {
        $this->db->where('cl.id', $client);
      }

      $dailySales['records'] = $this->db->get();

      //get total of the fields in job table
      $this->db->select('
        SUM(job.`payed_amount`) AS total,
        SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash` ELSE 0 END) AS cash,
        SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash` * (5/100) ELSE 0 END) AS vat_cash,
        SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit` ELSE 0 END) AS credit,
        SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit` * (5/100) ELSE 0 END) AS vat_credit,
        SUM(`job`.`points`) AS `points`,
        SUM(parkpass) AS parkpass,
		SUM(CASE WHEN parkpass LIKE "%yes%" THEN (job.total_bill- job.payed_amount) ELSE 0 END ) AS parkpass_amount,
		
		SUM(CASE WHEN validation LIKE "%true%" THEN (job.total_bill- job.payed_amount) ELSE 0 END) AS validation_discount, 
		(CASE WHEN job.validation LIKE "%true%" THEN "Yes" ELSE "No" END) AS validation,
							
        SUM(
          CASE
            WHEN job.`payment_type` = "Exception" 
            THEN job.`total_bill` 
            ELSE 0 
          END
        ) AS exception,
        
        SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND((CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_exception,
        ROUND((CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_vat_exception,
        SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
        ROUND((CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS vat_credit_exception,

        SUM(
          CASE
            WHEN job.`payment_type` = "Exception" 
            THEN job.`total_bill` - job.`payed_amount` 
            ELSE 0 
          END
        ) AS exception_notpaid
      ');

      $this->db->from('job');

      if($dates)
        {
           $val=$dates;
           $date = explode('-',$val);
           $days="";  
          if(trim($date[1])==trim($date[0]))
           {
             $where='job.handover_time BETWEEN "'.date('Y-m-d ',strtotime($date[0])).'" AND "'.date('Y-m-d',strtotime('+1 day',strtotime($date[0]))).'"'.$days;
             $this->db->where($where);
           }
           else
           {
              $temp=false;
              if($days)
              {
                $x=$days;
                $temp="'" . implode("','", $x) . "'";  
              }
              
              if($temp)
              {
                $days=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
              }
              
              $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$days;
              $this->db->where($where); 
           }
          
        }
        if($days)
        {
           $x=$days;
           $temp="'" . implode("','", $x) . "'"; 
           $where=" DAYNAME(job.`handover_time`) IN (".$temp.")";  
           $this->db->where($where);
        } 
        if($acctype)
        {
           $val=$acctype; 
           $this->db->where('account_types.id',$val); 
        }
        if($location)
        {
           $x=$location;
           $temp="'" . implode("','", $x) . "'"; 
           $where=" job.location IN (".$temp.")";
           $this->db->where($where);    
        }
        if($car)
        {
           $x=$car;
           $temp="'" . implode("','", $x) . "'"; 
           $where=" car.Make IN (".$temp.")";
           $this->db->where($where);    
        }
        if($staff)
        {
           $val=$staff;
           $where=" job.id IN(".$temp2.")";
           $this->db->where($where);  
        }
        if($gate) {
          $this->db->where('entry_gate', $gate);
        }
        if($service) {
          $this->db->where('service_type', $service);
        }

      $dailySales['total'] = $this->db->get();

      return $dailySales;

    }


    function saleSummary($cdate, $location, $service) {

        $this->db->select('
        DATE(job.`handover_time`) AS date,
        SUM(job.`payed_amount`) AS totalSale,
        SUM(CASE WHEN job.parkpass IS NOT NULL AND job.parkpass NOT LIKE "%No%" AND job.parkpass != "" THEN (job.total_bill - job.payed_amount) ELSE 0 END) AS parkpass,
        
        (CASE WHEN account_types.id = 1 AND cl.id = 2 THEN account_types.account_types ELSE cl.name END)  AS client_name,

        SUM(CASE  
        WHEN  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) < pcr.from  AND  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) <= pcr.to THEN pcr.rate 
        WHEN TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) > pcr.from  AND  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) <= pcr.to THEN pcr.rate 
        ELSE 0
        END )AS client_amount,

			 
	      SUM(CASE WHEN validation LIKE "%true%" THEN (job.total_bill- job.payed_amount) ELSE 0 END) AS validation_discount, 

        SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash` ELSE 0 END) - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS cash,
        ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS vat_cash,
        SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit` ELSE 0 END) - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS credit,
        ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS vat_credit,
        SUM(job.points) AS points,
        SUM(CASE WHEN job.`payment_type`="Exception" THEN job.`payed_amount` ELSE 0 END) AS exception,

        SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_exception,
        ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_vat_exception,
        SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
        ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS vat_credit_exception,


        SUM(CASE WHEN job.`payment_type`="Exception" THEN job.`total_bill`-job.`payed_amount`ELSE 0 END) AS exception_notpaid
          ');
        $this->db->from('job');
        $this->db->join('customers c','c.id = job.`account_id`','LEFT');
        $this->db->join('client cl','cl.id = c.client_id','LEFT'); 
        $this->db->join('account','account.`id` = job.`account_id`'); 
        $this->db->join('account_types','account_types.`id` = account.`account_types_id`');
        $this->db->join('pass_client_rates pcr','pcr.client_id = cl.id AND pcr.account_type = account_types.id','LEFT'); 	
  

        if($cdate)
        {
        $val   = $cdate;
        $date  = explode('-',$val);
        $days  = "";
        $dayss = "";
        if(trim($date[1])==trim($date[0]))
        {
          $where=" job.`handover_time` LIKE '".date('Y-m-d H:i:s A',strtotime($date[0]))."%'";
          $this->db->where($where);
        }
        else
        {
          $temp=false;
          if($days)
          {
            $x=$days;
            $temp="'" . implode("','", $x) . "'";  
          }
              
          if($temp)
          {
            $dayss=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
          }
              
          $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$dayss;
          

          $this->db->where($where); 
        }
           
     } 
     if($location)
     {
        $val=$location;
        $this->db->where_in('job.location',$val);
     }

     if($service) {
       $this->db->where('job.service_type', $service);
     }

        
        $this->db->group_by('ROUND(UNIX_TIMESTAMP(job.`handover_time`) DIV 86400),cl.id');
        $results['sales'] = $this->db->get(); 


        // get the total for each field

        $this->db->select(' 
        ROUND(SUM(job.`payed_amount`),3) AS total,
        ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash` ELSE 0 END),3)  - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS cash,
        ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS vat_cash,
        ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit` ELSE 0 END),3)  - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS credit,
        ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS vat_credit,
        ROUND(SUM(CASE  
        WHEN  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) < pcr.from  AND  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) <= pcr.to THEN pcr.rate 
        WHEN TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) > pcr.from  AND  TIMESTAMPDIFF(MINUTE,job.`handover_time`,job.receive_time) <= pcr.to THEN pcr.rate 
        ELSE 0
        END ))AS client_amount,
		    SUM(CASE WHEN job.parkpass IS NOT NULL AND job.parkpass NOT LIKE "%No%" AND job.parkpass != "" THEN (job.total_bill - job.payed_amount) ELSE 0 END) AS parkpass,
        SUM(job.points) AS points,
        ROUND(SUM(CASE WHEN validation LIKE "%true%" THEN (job.total_bill- job.payed_amount) ELSE 0 END),3)  AS validation_discount, 

        SUM(
          CASE
            WHEN job.`payment_type` = "Exception" 
            THEN job.`payed_amount` 
            ELSE 0 
          END
        ) AS exception,

        ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END),3)  -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_exception,
        ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_vat_exception,
        ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END),3)  -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
        ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS vat_credit_exception,

        ROUND(SUM(
          CASE
            WHEN job.`payment_type` = "Exception" 
            THEN job.`total_bill` - job.`payed_amount` 
            ELSE 0 
          END
        ),3) AS exception_notpaid
        ');
        $this->db->from('job');
        $this->db->join('customers c','c.id = job.`account_id`','LEFT');
        $this->db->join('client cl','cl.id = c.client_id','LEFT'); 
        $this->db->join('account','account.`id` = job.`account_id`'); 
        $this->db->join('account_types','account_types.`id` = account.`account_types_id`');
        $this->db->join('pass_client_rates pcr','pcr.client_id = cl.id AND pcr.account_type = account_types.id','LEFT'); 	
  
        if($cdate)
        {
        $val=$cdate;
        $date = explode('-',$val);
        $days="";
        $dayss="";   
        if(trim($date[1])==trim($date[0]))
        {
          $where=" job.`handover_time` LIKE '".date('Y-m-d H:i:s A',strtotime($date[0]))."%'";
          $this->db->where($where);
        }
        else
        {
          $temp=false;
          if($days)
          {
            $x=$days;
            $temp="'" . implode("','", $x) . "'";  
          }
              
          if($temp)
          {
            $dayss=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
          }
              
          $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$dayss;
          $this->db->where($where); 
        }
           
     } 
     if($location)
     {
        $val=$location;
        $this->db->where_in('job.location',$val);
     }
     if($service) {
      $this->db->where('job.service_type', $service);
    }
     $results['total'] = $this->db->get();

        return $results;
    }

    function saleSummaryGateWise($cdate, $location, $service,$gate) {

      $this->db->select('
      DATE(job.`handover_time`) AS date,
      SUM(job.`payed_amount`) AS totalSale,
      SUM(job.parkpass) AS parkpass,
      SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash` ELSE 0 END) - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS cash,
      ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS vat_cash,
      SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit` ELSE 0 END) - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS credit,
      ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS vat_credit,

      SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_exception,
      ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_vat_exception,
      SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
      ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS vat_credit_exception,


      SUM(job.points) AS points,
      SUM(CASE WHEN job.`payment_type`="Exception" THEN job.`payed_amount` ELSE 0 END) AS exception,
      SUM(CASE WHEN job.`payment_type`="Exception" THEN job.`total_bill`-job.`payed_amount`ELSE 0 END) AS exception_notpaid
        ');
      $this->db->from('job');


      if($cdate)
      {
      $val   = $cdate;
      $date  = explode('-',$val);
      $days  = "";
      $dayss = "";
      if(trim($date[1])==trim($date[0]))
      {
        $where=" job.`handover_time` LIKE '".date('Y-m-d H:i:s A',strtotime($date[0]))."%'";
        $this->db->where($where);
      }
      else
      {
        $temp=false;
        if($days)
        {
          $x=$days;
          $temp="'" . implode("','", $x) . "'";  
        }
            
        if($temp)
        {
          $dayss=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
        }
            
        $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$dayss;
        

        $this->db->where($where); 
      }
         
   } 
   if($location)
   {
      $val=$location;
      $this->db->where_in('job.location',$val);
   }

   if($gate) {
    $this->db->where('entry_gate', $gate);
  }

   if($service) {
     $this->db->where('job.service_type', $service);
   }

      
      $this->db->group_by('ROUND(UNIX_TIMESTAMP(job.`handover_time`) DIV 86400)');
      $results['sales'] = $this->db->get(); 


      // get the total for each field

      $this->db->select(' 
      SUM(job.`payed_amount`) AS total,
      SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash` ELSE 0 END) - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS cash,
      ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`cash`/105 * 5 ELSE 0 END), 3) AS vat_cash,
      SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit` ELSE 0 END) - ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS credit,
      ROUND(SUM(CASE WHEN job.`payment_type`!="Exception" THEN job.`credit`/105 * 5 ELSE 0 END), 3) AS vat_credit,
      SUM(parkpass) AS parkpass,

      SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_exception,
      ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.cash != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS cash_vat_exception,
      SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "null" THEN job.`payed_amount` ELSE 0 END) -ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS credit_exception,
      ROUND(SUM(CASE WHEN job.`payment_type`="Exception" AND job.credit != "NULL" THEN job.`payed_amount`/105 * 5 ELSE 0 END), 3) AS vat_credit_exception,


      SUM(job.points) AS points,
      SUM(
        CASE
          WHEN job.`payment_type` = "Exception" 
          THEN job.`payed_amount` 
          ELSE 0 
        END
      ) AS exception,
      SUM(
        CASE
          WHEN job.`payment_type` = "Exception" 
          THEN job.`total_bill` - job.`payed_amount` 
          ELSE 0 
        END
      ) AS exception_notpaid
      ');
      $this->db->from('job');

      if($cdate)
      {
      $val=$cdate;
      $date = explode('-',$val);
      $days="";
      $dayss="";   
      if(trim($date[1])==trim($date[0]))
      {
        $where=" job.`handover_time` LIKE '".date('Y-m-d H:i:s A',strtotime($date[0]))."%'";
        $this->db->where($where);
      }
      else
      {
        $temp=false;
        if($days)
        {
          $x=$days;
          $temp="'" . implode("','", $x) . "'";  
        }
            
        if($temp)
        {
          $dayss=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
        }
            
        $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$dayss;
        $this->db->where($where); 
      }
         
   } 
   if($location)
   {
      $val=$location;
      $this->db->where_in('job.location',$val);
   }

   if($gate) {
    $this->db->where('entry_gate', $gate);
  }

   if($service) {
    $this->db->where('job.service_type', $service);
  }
  
   $results['total'] = $this->db->get();

      return $results;
  }

  function hourlyReport($cdate, $location, $service,$gate) {

    $this->db->select(" handover_time,
    SUM( job.`payed_amount`)  AS total_cash, 
    SUM(job.`payed_amount` ) - ROUND(SUM( job.`payed_amount`/105 * 5), 3) AS 'amount_value_without_vat', 
    ROUND(SUM(job.`payed_amount`/105 * 5 ), 3) AS vat_amount, 
    COUNT(*) AS 'number_of_items' 
    ");
    $this->db->from('job');

    if($cdate)
    {
    $val   = $cdate;
    $date  = explode('-',$val);
    $days  = "";
    $dayss = "";
    if(trim($date[1])==trim($date[0]))
    {
      $where=" job.`handover_time` LIKE '".date('Y-m-d H:i:s A',strtotime($date[0]))."%'";
      $this->db->where($where);
    }
    else
    {
      $temp=false;
      if($days)
      {
        $x=$days;
        $temp="'" . implode("','", $x) . "'";  
      }
          
      if($temp)
      {
        $dayss=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
      }
          
      $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$dayss;
      

      $this->db->where($where); 
    }
       
 } 
 if($location)
 {
    $val=$location;
    $this->db->where_in('job.location',$val);
 }

 if($gate) {
  $this->db->where('exit_gate', $gate);
}

 if($service) {
   $this->db->where('job.service_type', $service);
 }

    
    $this->db->group_by('DAY(handover_time), HOUR(handover_time)');
    $results['sales'] = $this->db->get(); 


    // get the total for each field

    $this->db->select("
    SUM( ROUND(job.`payed_amount`,3)) AS total_cash, 
    SUM(ROUND(job.`payed_amount`,3) ) - SUM( ROUND((job.`payed_amount`/105 * 5),3)) AS 'amount_value_without_vat',
    SUM(ROUND((job.`payed_amount`/105 * 5 ), 3)) AS vat_amount,  
    COUNT(*) AS 'number_of_items' 
    ");

    $this->db->from('job');

    if($cdate)
    {
    $val=$cdate;
    $date = explode('-',$val);
    $days="";
    $dayss="";   
    if(trim($date[1])==trim($date[0]))
    {
      $where=" job.`handover_time` LIKE '".date('Y-m-d H:i:s A',strtotime($date[0]))."%'";
      $this->db->where($where);
    }
    else
    {
      $temp=false;
      if($days)
      {
        $x=$days;
        $temp="'" . implode("','", $x) . "'";  
      }
          
      if($temp)
      {
        $dayss=" AND DAYNAME(job.`handover_time`) IN (".$temp.")";
      }
          
      $where='job.handover_time BETWEEN "'.date('Y-m-d H:i:s A',strtotime($date[0])).'" AND "'.date('Y-m-d H:i:s A',strtotime($date[1])).'"'.$dayss;
      $this->db->where($where); 
    }
       
 } 
 if($location)
 {
    $val=$location;
    $this->db->where_in('job.location',$val);
 }

 if($gate) {
  $this->db->where('exit_gate', $gate);
}

 if($service) {
  $this->db->where('job.service_type', $service);
}

  $results['total'] = $this->db->get();
  return $results;
}

}

?>