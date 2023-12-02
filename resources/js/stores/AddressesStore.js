import {defineStore} from "pinia"
import {useAxios} from "@vueuse/integrations/useAxios";

export const useAddressesStore = defineStore('addresses', function (){
    const { data: addresses, isFinished: addressesFetched, execute: fetchAddresses } = useAxios('/api/addresses')

    return {
        addresses,
        addressesFetched,
        fetchAddresses
    }
})
