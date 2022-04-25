<template>

<div id="home">
  <div id="register">
    <img src="../assets/logo_1.png">
    <!--    <p><router-link :to="{name:'login'}"><button class="btn btn-primary" type="button" id="bt1">Connexion</button></router-link></p>-->
    <!--    <p><router-link :to="{name:'signup'}"><button class="btn btn-primary" type="button" id="bt2">Inscription</button></router-link></p>-->
    <p><button class="btn btn-primary" type="button" id="bt1" @click="addmodallogin(true)">Connexion</button></p>
    <p><button class="btn btn-primary" type="button" id="bt2"  @click="addmodalsignup(true)">Inscription</button></p>
  </div>
  <!--  <router-view></router-view>-->
  <!--  modallogin-->
  <div class="container">
    <form method="post" v-if="showAddlogin" novalidate  @click.prevent>
      <div class="form-group" :class="{'form-group-error':v$.R_U.$error}">
        <label for="reference">Référence</label>
        <input type="text" class="form-control" id="reference"  placeholder="Entrer Référence" v-model="R_U" required>
        <span class="error-feedback" v-if="v$.R_U.$error">
                     {{v$.R_U.$errors[0].$message}}
        </span>
      </div>
      <button type="submit" class="btn btn-primary" @click="routeres();">Connecter</button>
    </form>
  </div>
  <!--  modalsignup-->
  <div class="container">
    <div class="form_inscription" v-if="showuaddsignup">
      <div class="form-group" :class="{'form-group-error':v$.newuser.Nom.$error}">
        <label for="nom">Nom</label>
        <input type="text" class="form-control" id="nom"  placeholder="Entrer Nom"
               v-model="newuser.Nom">
        <span class="error-feedback" v-if="v$.newuser.Nom.$error">
                     {{v$.newuser.Nom.$errors[0].$message}}
        </span>
      </div>
      <div class="form-group" :class="{'form-group-error':v$.newuser.Prénom.$error}">
        <label for="prenom">Prénom</label>
        <input type="text" class="form-control" id="prenom"  placeholder="Entrer Prénom"
               v-model="newuser.Prénom">
        <span class="error-feedback" v-if="v$.newuser.Prénom.$error">
                     {{v$.newuser.Prénom.$errors[0].$message}}
        </span>
      </div>
      <div class="form-group" :class="{'form-group-error':v$.newuser.Date_de_naissance.$error}">
        <label for="date_naissance">date_naissance</label>
        <input type="date" class="form-control" id="date_naissance" placeholder="Date_naissance"
               v-model="newuser.Date_de_naissance">
        <span class="error-feedback" v-if="v$.newuser.Date_de_naissance.$error">
                     {{v$.newuser.Date_de_naissance.$errors[0].$message}}
        </span>
      </div>
      <button type="submit" class="btn btn-primary" @click="add_user();">Inscrire</button>
    </div>
  </div>
  <!--  modalreference-->
  <div class="container">
    <div class="form_inscription" v-if="show_ref">
      <div class="form-group">
              <strong>Reference : </strong><small>{{get_R_U}}</small>
      </div>
    </div>
  </div>
</div>

</template>
<script>
import useVuelidate from "@vuelidate/core"
import { required , minLength} from "@vuelidate/validators"
import axios from "axios";
export default {
    name: `acceuil`,
  data(){
    return {
      v$:useVuelidate(),
      errMsg: "",
      successMsg:"",
      connect_successMsg:"",
      is_db_connected:"",
      is_db_connectedMsg:"",
      showAddlogin:false,
      showuaddsignup:false,
      show_ref : false,
      newuser:{
        Nom:"",
        Prénom:"",
        Date_de_naissance:"",
      },
      R_U:"",
      get_R_U: "",
    }

  },
  mounted(){
    let Re=JSON.parse(localStorage.getItem("Reference_user"));
    if(Re){
      this.$router.push({name:'Dashbord'});
    }
  }
  ,
  validations(){
    return{
      R_U:{required},
      newuser:{
        Nom: {required, minLength:minLength(4)},
        Prénom:{required, minLength:minLength(4)},
        Date_de_naissance:{required, minLength:minLength(8)},
      }
    }
  }
  ,
  methods: {
    addmodallogin(val) {
      this.showAddlogin = val;
      this.showuaddsignup = false;
      this.show_ref = false;
    },
    addmodalsignup(val) {
      this.showuaddsignup = val;
      this.showAddlogin = false;
      this.show_ref = false;
    },
    get_ref(val) {
      this.show_ref = val;
      this.showAddlogin = false;
      this.showuaddsignup = false;
    },
    toFormData(obj){
      var fd=new FormData();
      for (var i in obj) {
        fd.append(i, obj[i]);
      }
      return fd;
    }
    ,
    async routeres(){
      this.v$.R_U.$validate();
      if(!this.v$.R_U.$error){
        let formData_rv=this.toFormData({R_U:this.R_U});
        this.addmodallogin(false);
        let res=await axios.post(`http://localhost/backend_brief_6/Login-u`,formData_rv
        );
        const resData=res.data;
        if (res.status == 200)
        {
          if (resData.error)
          {
            this.errMsg=resData.message;
          }else
          {
            if (resData.user_connecter)
            {
              this.connect_successMsg=resData.message_user;
              localStorage.setItem("Reference_user", JSON.stringify(resData.RF_user));
              this.R_U="";
              this.v$.R_U.$reset();
              this.$router.push({name:'Dashbord'});
            }else {
              this.connect_successMsg=resData.message_user;
              this.addmodallogin(true);
            }
          }
        }
      }else{
        console.log('login user: not validate successfully');
      }
    }
    ,
    async add_user(){
      this.v$.newuser.$validate();
      if(!this.v$.newuser.$error){
        let formData_rv=this.toFormData(this.newuser);
        this.addmodalsignup(false);
        let res=await axios.post(`http://localhost/backend_brief_6/Inscription-u`,formData_rv
        );
        const resData=res.data;
        if (res.status == 200){
          if (resData.error){
            this.errMsg=resData.message;
          }else {
            if(resData.user_daz){
              this.get_R_U=resData.RF_user;
              this.successMsg=resData.message_user;
              this.newuser={
                Nom:"",
                Prénom:"",
                Date_de_naissance:"",
              };
              this.get_ref(true);
              this.v$.newuser.$reset();
            }else{
              this.get_R_U=resData.RF_user;
              this.successMsg=resData.message_user;
              this.newuser={
                Nom:"",
                Prénom:"",
                Date_de_naissance:"",
              };
              this.get_ref(true);
              this.v$.newuser.$reset();
            }
          }
        }
      }else{
        console.log('add new user: not validate successfully');
      }
    },
  },
};
</script>
<style scoped>
/*body*/
/*{*/
/*  background-image: url("../assets/image1.jpg");*/
/*  background-repeat: no-repeat;*/
/*}*/
#home{
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  /*background-image: url("../assets/image1.jpg");*/
  /*background-size: 100% 110%;*/
  /*background-repeat: no-repeat;*/
}

img {
  width: 15%;
  margin-left:42%;
}
button#bt1 {
  background-color: #1396e8;
  border-color:#1396e8 ;
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
  width: 30%;
  margin-left: 35%;
}
button#bt2 {
  margin-left: 35%;
  background-color: #ff66c4;
  border-color:#ff66c4;
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
  width: 30%;
  margin-bottom: 10px;
}
/*login*/
form {
  padding: 20px;
  margin: auto;
  max-width: 30%;
  box-shadow: 5px 10px 8px 10px #888888;
  border-radius: 31px;
}
.form_inscription {
  padding: 20px;
  margin: auto;
  max-width: 30%;
  box-shadow: 5px 10px 8px 10px #888888;
  border-radius: 31px;
}
@media only screen and (max-width: 600px) {
  form {
    min-width:100%
  }
  .form_inscription {
    min-width:100%
  }
}
button.btn.btn-primary {
  MARGIN-TOP: 10px;
  background-color: #1396e8;
  border-color:#1396e8;
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
}
.form-group-error{
  color: red;
}
.form-group-error input{
  border-color: red;
}
label{
  font-family: poppins;
  font-weight:bold;
  font-style: italic;
}
</style>
