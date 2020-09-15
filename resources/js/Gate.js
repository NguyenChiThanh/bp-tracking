export default class Gate{

    constructor(user){
        this.user = user;
    }

    isAdmin(){
        return this.user.is_admin;
    }

    isMod(){
        return this.user.is_mod;
    }

    isPMCUser(){
        return this.user.is_pmc_user();
    }

    isPartnerUser(){
        return this.user.is_partner_user();
    }

}

