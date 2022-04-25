<?php 

class  Reservation
{

    static public function getAll_rv(){
        $query="SELECT *FROM rendez_vous ";
        $stmt = DB::connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    //        Delete
        static public function Delete($id){
            $query="SELECT *FROM rendez_vous  where id=:id";
            $stmt = DB::connect()->prepare($query);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                $query="DELETE FROM rendez_vous  where id=:id";
                $stmt = DB::connect()->prepare($query);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
    //        Add
        static public function Add($R_V){
            try {
                 $query="INSERT INTO rendez_vous (Date,Horaire,Reference)VALUES(:Date,:Horaire,:Reference)";
                 $stmt = DB::connect()->prepare($query);
                 $stmt->bindParam(':Date',$R_V['Date']);
                 $stmt->bindParam(':Horaire',$R_V['Horaire']);
                 $stmt->bindParam(':Reference',$R_V['Reference']);
                 if ($stmt->execute()) {
                         return 1;
                     } else {
                         return 0;
                     }
            } catch (PDOException $ex) {
                echo 'erreur'.$ex->getMessage();
            }
        }

    //        edit
    static public function Edit($R_V){
        try {
            $stmt = DB::connect()->prepare("UPDATE rendez_vous SET  Date=:Date,Horaire=:Horaire,Reference=:Reference where id=:id");
            $stmt->bindParam(':Date',$R_V['Date']);
            $stmt->bindParam(':Horaire',$R_V['Horaire']);
            $stmt->bindParam(':Reference',$R_V['Reference']);
            $stmt->bindParam(':id',$R_V['id']);
            if ($stmt->execute()) {
                return 1;
            } else {
                return 0;
            }
        } catch (PDOException $ex) {
            echo 'erreur'.$ex->getMessage();
        }
    }
}
?>