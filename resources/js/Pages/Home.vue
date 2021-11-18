<template>
    <form @submit.prevent="initializeAssign">
        <div>
            <label for="pdf">Selecione o pdf que deseja assinar</label>
            <br>
            <input type="file" name="pdf" id="pdf" @change="fileSelected">
        </div>
        <br>
        <br>
        <button v-if="!!initialize_form.cert_content" type="submit" >Assinar</button>
    </form>
    <br>
    <bry-select-certificate v-if="file_selected" @selected="selectCertificate"/>
</template>

<script>
import BrySelectCertificate from "../Components/BrySelectCertificate";
import axios from 'axios'
export default {
    name: "Home",
    data () {
        return {
            file_selected: false,
            initialize_form: {
                pdf: null,
                cert_content: null
            },
            assinatura: {},
            certificate: {}
        }
    },
    components: {
        BrySelectCertificate
    },
    methods: {
        async initializeAssign () {
            const formData = new FormData()
            formData.append('pdf', this.initialize_form.pdf)
            formData.append('cert_content', this.initialize_form.cert_content)
            const {data} = await axios.post('iniciar-assinatura', formData)
            this.assinatura = data
            await this.finalizeAssign(data)
        },

        forceDownload (link) {
            const element = document.createElement('a');
            element.href = link;
            element.download = 'Documento-assinado.pdf';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        },

        async finalizeAssign (assinatura) {
            const response = await BryExtension.sign(this.certificate.certId, JSON.stringify(assinatura))
            const {data} = await axios.post('finalizar-assinatura', response)
            this.forceDownload(data)
            console.log('FINALIZAR ASSINATURA', response)
        },

        fileSelected (file) {
            this.initialize_form.pdf = file.target.files[0]
            console.log('FILE', this.form)
            this.file_selected = true
        },

        selectCertificate (certificate) {
            console.log('CERTIFICADO SELECIONADO', certificate)
            this.certificate = certificate
            this.initialize_form.cert_content = certificate.certificateData
            this.file_selected = false
        }
    }
}
</script>

<style scoped>

</style>
