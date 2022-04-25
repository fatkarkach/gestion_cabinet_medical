<template>
<div id="home">
  <div class="container" v-if="!isdbconnected">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{isdbconnectedmsg}}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
    </div>
  </div>

  <div class="container" v-else>
    <!--    Show add rendez  button-->
    <div class="col-lg-12">
      <button class="btn btn-primary" id="add" @click="addmodaldialog(true)">
        Ajouter un rendez-vous
      </button>
    <button class="btn btn-primary bt2"  @click="logout()">
      Déconnection
    </button>
    </div>
    <!--    Show add student details tables-->
    <table class="table  table-striped caption-top">
      <caption>Listes des rendez-vous({{ rendezvous_data.length }}) </caption>
      <thead>
      <tr class="text-light text-center">
        <th>ID</th>
        <th>Date</th>
        <th>Horraire</th>
        <th>Référence</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>

      <tr class="text-center" v-for=" (rendez_vous,i) in rendezvous_data" :key="i">
        <td>{{++i}}</td>
        <td>{{ rendez_vous.Date }}</td>
        <td>{{rendez_vous.Horaire}}</td>
        <td>{{rendez_vous.Reference }}</td>
        <td>
          <button class="btn" @click="deletemodaldialog(true);selectrendez_vous(rendez_vous);"><img src="../assets/trash.svg"></button>&nbsp;
          <button class="btn"  @click="updatemodaldialog(true);selectrendez_vous(rendez_vous);"><img src="../assets/pencil.svg"></button>
        </td>
      </tr>
      </tbody>
    </table>
    <!--    show add new student modale-->
    <div class="overlay" v-if="showAddmodal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-primary">Ajouter Un Rendez-vous</h5>
            <button aria-hidden="true" @click="addmodaldialog(false)"><img src="../assets/x-s.svg" ></button>
          </div>
          <div class="modal-body p-4">
            <form method="post">
              <div class="form-floating mb-3" >
                <input v-model="newrendez_vous.Date" type="date" class="form-control" id="rendez-float" >
                <label for="rendez-float">Date</label>
              </div>
              <div class="form-floating mb-3" >
                <select v-model="newrendez_vous.Horaire"  class="form-control" id="horaire-float">
                  <option>12:33:37</option>
                  <option>04:33:39</option>
                </select>
                <label for="horaire-float">Horaire</label>

              </div>
              <hr class="bg-info">
              <div class="d-grid gap-2">
                <button class="btn btn-primary" @click="addnewrendez_vous()">Ajouter un rendez-vous</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
    <!--    show edit student modal-->
    <div class="overlay" v-if="showupdatemodal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-primary">Update User</h5>
            <button aria-hidden="true" @click="updatemodaldialog(false)"><img src="../assets/x-s.svg" ></button>
          </div>
          <div class="modal-body p-4">
            <form action="#" method="post">
              <div class="form-floating mb-3">
                <input v-model="currentrendez_vous.Date" type="date" class="form-control" id="daterendez-vous"  v-model.trim="currentrendez_vous.Date">
                <label for="daterendez-vous">Nome</label>
              </div>
              <div class="form-floating mb-3">
                <select v-model="currentrendez_vous.Horaire"  class="form-control" id="horairerendez-vous"  v-model.trim="currentrendez_vous.Horaire">
                  <option>32:33:37.000000</option>
                  <option>32:33:39.000000</option>
                </select>
                <label for="horairerendez-vous">Horaire</label>
              </div>
              <hr class="bg-info">
              <div class="d-grid gap-2">
                <button class="btn btn-primary" @click="updatenewuser()">Update Rendez-vous</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
    <!--    show delete student modal-->
    <div class="overlay" v-if="showdeletemodal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-primary">Delete User</h5>
            <button aria-hidden="true" @click="deletemodaldialog(false)"><img src="../assets/x-circle.svg"></button>
          </div>
          <div class="modal-body p-4">
            <form action="#" method="post" @click.prevent>
              <p>
                <span></span> {{currentrendez_vous.Date}}<br>
                <span></span>{{currentrendez_vous.Horaire}}<br>
              </p>
              <hr class="bg-info">
              <div class="d-grid gap-2">
                <button class="btn btn-primary" @click="deleterendez_vous">Supprimer le Rendez-Vous</button>
                <button class="btn btn-primary" id="bt2" @click="deletemodaldialog(false)"> NON </button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</template>
<script>
import useValidate from "@vuelidate/core";
// import { required,email,minLength } from "@vuelidate/validators";
import  axios from "axios";

export default{
  name:`dashbord`,
  data(){
    return{
      errMsg:"",
      successMsg:"",
      rendezvous_data:[],
      showAddmodal:false,
      showupdatemodal:false,
      showdeletemodal:false,
      showdeleteallmodal:false,
      isdbconnectedmsg:"",
      isdbconnected:"",
      v$:useValidate(),
      newrendez_vous:{
        Date:'',
        Horaire:'',
        Reference: "",
      },
      get_R_U: "",
      currentrendez_vous:{},

    }
  },
  mounted() {
    this.getrendez_vousdata();
    this.db_connection();
    let Re=JSON.parse(localStorage.getItem("Reference_user"));
    this.get_R_U=Re;
    if(!Re){
      this.$router.push({name:'acceuil'});
    }
  },
  methods:{
    async logout() {
      let res= await axios.get("http://localhost/backend_brief_6/Logout-u")
      const restdata=res.data;
      if(res.status==200)
      {
        if(restdata.error){
          //si il ya erreur
          this.errMsg=restdata.message;

        }else{
          localStorage.removeItem('Reference_user');
          this.$router.push({name:'acceuil'});
        }

      }
    },
    addmodaldialog(val){
      this.showAddmodal=val;
    },
    updatemodaldialog(val){
      this.showupdatemodal=val;
    },
    deletemodaldialog(val){
      this.showdeletemodal=val;
    },
    async  getrendez_vousdata(){
      let res= await axios.get("http://localhost/backend_brief_6/Read-rv")
      const restdata=res.data;
      if(res.status==200)
      {
        if(restdata.error){
          //si il ya erreur
          this.errMsg=restdata.message;

        }else{
          this.rendezvous_data=restdata.r_v.filter((ele)=>ele.Reference==this.get_R_U);
        }

      }
    },
    async  db_connection(){
      let res= await axios.get("http://localhost/backend_brief_6/")
      const restdata=res.data;
      this.isdbconnected=restdata.is_db_connected;
      this.isdbconnectedmsg=restdata.connection_msg;
    },
    async  deleterendez_vous(){
      let a=this.toformdata(this.currentrendez_vous);
      this.clearoldmsgs();
      this.deletemodaldialog(false);
      let res= await axios.post("http://localhost/backend_brief_6/Delete-rv", a);
      const restdata=res.data;
      if(res.status==200)
      {
        if(restdata.error){
          //si il ya erreur
          this.errMsg=restdata.message;

        }else{
          this.successMsg=restdata.message;
          this.currentrendez_vous= {};
          this.getrendez_vousdata();
        }

      }
    },
    toformdata(obj)
    {
      var fd=new FormData();
      for(var i in obj)
      {
        // console.log(i + "=>" + obj[i]);
        fd.append(i,obj[i]);
      }
      return fd;

    },
    clearoldmsgs()
    {
      this.errMsg="";
      this.successMsg="";

    },
    selectrendez_vous(std){
      this.currentrendez_vous= std;
    },
    //ajouter nouveau rendez vous
    async addnewrendez_vous(){
      this.newrendez_vous.Reference=this.get_R_U;
      let Data_rv=this.toformdata(this.newrendez_vous);
      this.addmodaldialog(false);
      let res=await axios.post("http://localhost/backend_brief_6/Add-rv",Data_rv);
      const resData=res.data;
      if (res.status == 200){
        if (resData.error){
          console.log(resData.message);
        }else {
          this.newrendez_vous={
            Date: "",
            Horaire: "",
            Reference: ""
          };
          this.getrendez_vousdata();
        }
      }
    },
    //update rendez-vous
    async updatenewuser(){
      let formData_rv=this.toformdata(this.currentrendez_vous);
      this.updatemodaldialog(false);
      let res=await axios.post("http://localhost/backend_brief_6/Update-rv",formData_rv);
      const resData=res.data;
      if (res.status == 200){
        if (resData.error){
          console.log(resData.message);
        }else {
          this.currentrendez_vous={};
          this.getrendez_vousdata();
          this.currentrendez_vous.$reset();
        }
      }
    },
  },
}

</script>
<style scoped>
.container {
  margin-top: 75px;
}
#home{
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-image: url("../assets/image1.jpg");
  background-repeat: no-repeat;

  background-size: 100% 110%;
}
.modal-dialog{
  pointer-events: auto !important;
}
.overlay{
  position: fixed;
  top:0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color:rgb(0 0 0 / 47%);
}
.form-group-error{
  color: red;
}
.form-group-error input{
  border-color: red;
}
button.btn.btn-primary {
  background-color: #1396e8;
  border-color:#1396e8;
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
  border-radius:30px;
  color: black;
}
th{
  background-color:#9f9f9f;
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
  color: black;
}

button#bt2 {
  background-color: #ff66c4;
  border-color:#ff66c4;
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
}
button.btn.btn-primary.bt2 {
  background-color: #ff66c4;
  border-color:#ff66c4;
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
  margin-left: 2%;
}


</style>