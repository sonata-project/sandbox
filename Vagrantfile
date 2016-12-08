Vagrant.require_version ">= 1.5"

Vagrant.configure("2") do |config|

    config.vm.box = "ubuntu/trusty64"
    config.vm.network :private_network, ip: "192.168.33.99"
    config.ssh.forward_agent = true

    #Configure the VM
    config.vm.provider :virtualbox do |v|
        v.name = "sonata_box"
        v.customize [
            "modifyvm", :id,
            "--name", "sonata_box",
            "--memory", 4096,
            "--natdnshostresolver1", "on",
            "--cpus", 4,
        ]
    end

    #Handle synced folders
    config.vm.synced_folder "./", "/vagrant", :nfs => true, :mount_options => ['nolock,vers=3,tcp']

    #Install Ansible and provision the VM
    config.vm.provision "shell", :inline => "/vagrant/.ansible/install_requirements.sh"
    config.vm.provision "shell", :inline => "/vagrant/.ansible/install_data.sh"
end
