<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->string('Matri_Elev', 10)->unique();
            $table->smallInteger('Code_Grp')->nullable();
            $table->string('Nom_Elev', 50)->nullable();
            $table->string('Sexe_Elev', 1)->nullable();
            $table->integer('Code_Ste')->nullable();
            $table->integer('Code_Eta')->nullable();
            $table->integer('Sub_Eta')->nullable();
            $table->string('Code_Reg', 1)->nullable();
            $table->integer('Code_Brs')->nullable();
            $table->integer('Code_Opt')->nullable();
            $table->decimal('Mont_Opt', 19, 4)->nullable();
            $table->string('Code_Detcla', 10)->nullable();
            $table->string('Nom_Cla', 100)->nullable();
            $table->integer('Code_Nat')->nullable();
            $table->string('Lieunais_Elev', 20)->nullable();
            $table->string('Datenais_Elev')->nullable();
            $table->string('TelBurResp_Elev', 30)->nullable();
            $table->string('Actenais_Elev', 20)->nullable();
            $table->string('NomPere_Elev', 20)->nullable();
            $table->string('NomMere_Elev', 20)->nullable();
            $table->string('EtablOrig_Elev', 25)->nullable();
            $table->string('ClassOrig_Elev', 20)->nullable();
            $table->string('AnneeSco_Orig', 9)->nullable();
            $table->string('DateEntre_Elev')->nullable();
            $table->integer('Code_Niv');
            $table->string('AnneeSco_Elev', 9)->nullable();
            $table->string('SerieBac_Elev', 20)->nullable();
            $table->string('DateObtBac_Elev')->nullable();
            $table->boolean('Redouble_Elev')->nullable();
            $table->string('Cycle_Elev', 2)->nullable();
            $table->string('NomResp_Elev', 30)->nullable();
            $table->string('ProfResp_Elev', 30)->nullable();
            $table->string('TitreResp_Elev', 4)->nullable();
            $table->string('AdresResp_Elev', 30)->nullable();
            $table->string('VilleResp_Elev', 15)->nullable();
            $table->string('TelDomResp_Elev', 30)->nullable();
            $table->integer('MontantSco_Elev')->nullable();
            $table->integer('SoldSco_Elev')->nullable();
            $table->float('Remise_Elev', 24, 0)->nullable();
            $table->integer('Bourse_Elev')->nullable();
            $table->integer('SoldBourse_Elev')->nullable();
            $table->boolean('DejaRegle')->nullable();
            $table->binary('Photo')->nullable();
            $table->text('Comment')->nullable();
            $table->string('Condition_Elev', 50)->nullable();
            $table->integer('Cotisation')->default(4000);
            $table->integer('SoldFraisExam')->nullable();
            $table->string('Prenom_Elev', 100)->nullable();
            $table->integer('scolFDFP')->nullable();
            $table->integer('idFDFP')->nullable();
            $table->string('idPreinscription', 12)->nullable();
            $table->string('Reservation', 10)->nullable();
            $table->string('numtabl', 20)->nullable();
            $table->string('numatri', 20)->nullable();
            $table->integer('totbac')->nullable();
            $table->string('matpc', 20)->nullable();
            $table->string('celetud', 30)->nullable();
            $table->string('teletud', 30)->nullable();
            $table->string('villetud', 50)->nullable();
            $table->string('cometud', 50)->nullable();
            $table->string('mailetud', 200)->nullable()->unique();
            $table->integer('reduction')->nullable();
            $table->string('Etab_source', 25)->nullable();
            $table->integer('Avoir')->nullable();
            $table->string('natPiece', 150)->nullable();
            $table->integer('montantExamen')->nullable();
            $table->boolean('Admission_Annee_Sup')->nullable();
            $table->boolean('Cloture_Peda')->nullable();
            $table->boolean('Extrait_naissance')->nullable();
            $table->boolean('Photocopie_diplômes')->nullable();
            $table->boolean('Photocopie_Legalise_BAC')->nullable();
            $table->boolean('Photocopie_Bulletins')->nullable();
            $table->boolean('Photo_identité')->nullable();
            $table->boolean('Fiche_demande_inscription')->nullable();
            $table->boolean('Fiche_médicale')->nullable();
            $table->boolean('Cinq_enveloppes')->nullable();
            $table->boolean('Cinq_timbres')->nullable();
            $table->boolean('Deuxieme_Extrait_naissance')->nullable();
            $table->boolean('Deuxieme_Photocopie_Legalise_BAC')->nullable();
            $table->string('DateInscri_Eleve')->nullable();
            $table->string('mailparent', 200)->nullable();
            $table->boolean('Inscrit_Carte')->nullable();
            $table->string('idperm', 20)->nullable();
            $table->boolean('Inscrit_Sous_Titre')->nullable();
            $table->boolean('Inscrit_Sous_Bulletin')->nullable();
            $table->float('MoySemPremier', 24, 0)->nullable();
            $table->float('MoySemDeuxieme', 24, 0)->nullable();
            $table->float('MoyAnnuelle', 24, 0)->nullable();
            $table->integer('TotalCreditSemPremier')->nullable();
            $table->integer('TotalCreditSemDeuxieme')->nullable();
            $table->text('rememberToken')->nullable();
            $table->text('password')->nullable();
            $table->boolean('active')->default(false);
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etudiants');
    }
};
