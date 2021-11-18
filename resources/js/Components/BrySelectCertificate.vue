<template>
    <div class="wrapper">
        <div class="card-wrapper">
            <div class="card-title has-text-weight-bold">
                Meus Certificados
            </div>
            <ul class="certificates">
                <li v-for="certificate in certificates" :key="certificate" class="certificate-item">
                    <div class="description">
                        <h2>{{ certificate.name }}</h2>
                        <p>{{ certificate.issuer }}</p>
                        <p>{{ certificate.email }}</p>
                    </div>
                    <button class="ver" @click="selectCertify(certificate)">
                        Selecionar
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SelectCertificate',
    data () {
        return {
            certificates: {}
        }
    },
    mounted () {
        BryExtension.listCertificates().then((certificates) => {
            this.certificates = certificates
        })
    },
    methods: {
        selectCertify (certificate) {
            this.$emit('selected', certificate)
        }
    }
}
</script>

<style lang="scss" scoped>
.wrapper {
    h1 {
        text-align: center;
        font-size: 22px;
    }
    .card-wrapper {
        .certificates {
            padding: 10px;
            border-radius: 5px;
            &:nth-child(even) {
                box-shadow: 0 0 12px rgba(0, 0, 0, .1);
            }
            .certificate-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 20px 0;
                &:nth-child(odd) {
                    padding-bottom: 15px;
                    border-bottom: 1px solid rgba(0, 0, 0, .1);
                }
                h2 {
                    font-size: 20px;
                    font-weight: 700 ;
                }
                p {
                    font-weight: 500 !important;
                }
                button {
                    width: 150px;
                    padding: 10px 20px;
                    font-size: 18px;
                    font-weight: 700 !important;
                }
            }
        }
    }
}
</style>
