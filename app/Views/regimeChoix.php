<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitaRégime — Choisir un objectif</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <style>
        :root {
            --primary: #2ecc71;
            --secondary: #3498db;
            --danger: #e74c3c;
            --warning: #f1c40f;
            --dark: #2c3e50;
            --light: #f8f9fa;
        }

        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; color: var(--dark); margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 0 auto; padding: 40px 20px; }
        
        /* Header & IMC */
        .header-profile { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 40px; }
        .imc-badge { display: inline-block; padding: 8px 15px; border-radius: 20px; color: white; font-weight: bold; margin-top: 10px; }
        
        /* Objectifs */
        .objectif-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .objectif-card { background: white; padding: 30px; border-radius: 15px; text-align: center; cursor: pointer; transition: 0.3s; border: 2px solid transparent; }
        .objectif-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .objectif-card.selected { border-color: var(--primary); background-color: #f0fff4; }
        .obj-icon { font-size: 3rem; margin-bottom: 15px; display: block; }

        /* Régimes */
        .regime-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); animation: fadeIn 0.5s ease-in-out; }
        .progression-bar { height: 10px; background: #eee; border-radius: 5px; overflow: hidden; margin: 5px 0 15px 0; }
        .fill { height: 100%; transition: width 0.5s; }
        
        .prix-tag { display: inline-block; background: var(--light); padding: 6px 12px; border-radius: 8px; margin: 5px 5px 0 0; font-size: 0.85rem; border: 1px solid #ddd; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .btn-select { background: var(--primary); color: white; border: none; padding: 12px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; margin-top: 20px; transition: 0.2s; }
        .btn-select:hover { background: #27ae60; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-profile">
        <h1 style="margin:0;">Salut, <?php echo $user->prenom; ?> !</h1>
        <p style="color: #666; margin: 10px 0;">Basé sur vos données (<?php echo $user->poids; ?>kg, <?php echo $user->taille; ?>cm) :</p>
        <div style="font-size: 1.2rem;">
            Votre IMC est de <strong><?php echo $imc; ?></strong>
            <span class="imc-badge" style="background-color: <?php 
                if($imc < 18.5) echo "#e67e22"; // Maigreur
                elseif($imc < 25) echo "#2ecc71"; // Normal
                else echo "#e74c3c"; // Surpoids/Obèse
            ?>;">
                <?php 
                    if($imc < 18.5) echo "Maigreur";
                    elseif($imc < 25) echo "Poids Normal";
                    elseif($imc < 30) echo "Surpoids";
                    else echo "Obésité";
                ?>
            </span>
        </div>
    </div>

    <h2 style="text-align:center; margin-bottom:30px;">Quel est votre objectif aujourd'hui ?</h2>
    <div class="objectif-grid">
        <div class="objectif-card" onclick="selectObjectif(this, 'reduction')">
            <span class="obj-icon">📉</span>
            <h3>Réduire</h3>
            <p style="font-size: 0.8rem; color: #888;">Perdre du poids sainement</p>
        </div>
        <div class="objectif-card" onclick="selectObjectif(this, 'augmentation')">
            <span class="obj-icon">📈</span>
            <h3>Grossir</h3>
            <p style="font-size: 0.8rem; color: #888;">Prendre de la masse</p>
        </div>
        <div class="objectif-card" onclick="selectObjectif(this, 'equilibre')">
            <span class="obj-icon">⚖️</span>
            <h3>IMC Idéal</h3>
            <p style="font-size: 0.8rem; color: #888;">Stabiliser mon poids</p>
        </div>
    </div>

    <div id="section-regimes" style="display:none;">
        <h2 style="margin-bottom:25px;">Régimes recommandés :</h2>
        <div id="grid-regimes" style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            </div>
    </div>
</div>

<script>
function selectObjectif(element, type) {
    // 1. UI: Sélection visuelle
    document.querySelectorAll('.objectif-card').forEach(card => card.classList.remove('selected'));
    element.classList.add('selected');

    // 2. AJAX: Récupération des régimes via ta nouvelle route /api/regimes/
    fetch('<?php echo site_url("api/regimes/"); ?>' + type)
        .then(response => response.json())
        .then(data => {
            const section = document.getElementById('section-regimes');
            const grid = document.getElementById('grid-regimes');
            section.style.display = 'block';
            grid.innerHTML = '';

            if (data.length === 0) {
                grid.innerHTML = '<p>Désolé, aucun régime ne correspond à cet objectif pour le moment.</p>';
                return;
            }

            data.forEach(r => {
                grid.innerHTML += `
                    <div class="regime-card">
                        <h3 style="margin-top:0;">${r.nom}</h3>
                        <p style="font-size:0.85rem; color:#666;">${r.description}</p>
                        
                        <div style="font-weight:bold; color:var(--primary); margin: 15px 0;">
                            Variation : ${r.variation_poids > 0 ? '+' : ''}${r.variation_poids} kg / semaine
                        </div>

                        <div style="font-size:0.8rem; margin-bottom:5px;">Composition des repas :</div>
                        
                        <small>Viande (${r.pourcentage_viande}%)</small>
                        <div class="progression-bar"><div class="fill" style="width:${r.pourcentage_viande}%; background:var(--danger);"></div></div>
                        
                        <small>Poisson (${r.pourcentage_poisson}%)</small>
                        <div class="progression-bar"><div class="fill" style="width:${r.pourcentage_poisson}%; background:var(--secondary);"></div></div>
                        
                        <small>Volaille (${r.pourcentage_volaille}%)</small>
                        <div class="progression-bar"><div class="fill" style="width:${r.pourcentage_volaille}%; background:var(--warning);"></div></div>

                        <div style="margin-top:20px; border-top: 1px dashed #eee; pt:15px;">
                            <p style="font-size:0.75rem; font-weight:bold; margin-bottom:8px; color:#999;">TARIFS DISPONIBLES :</p>
                            ${r.tarifs.map(t => `
                                <div class="prix-tag">
                                    ${t.duree_semaines} sem : <strong>${parseInt(t.prix).toLocaleString()} Ar</strong>
                                </div>
                            `).join('')}
                        </div>

                        <button class="btn-select" onclick="commander(${r.id})">Choisir ce programme</button>
                    </div>
                `;
            });
        })
        .catch(error => console.error('Erreur AJAX:', error));
}

function commander(id) {
    alert("Souscription au régime #" + id + " en cours...");
    // Ici tu peux rediriger : window.location.href = "<?php echo site_url('paiement/regime/'); ?>" + id;
}
</script>

</body>
</html>