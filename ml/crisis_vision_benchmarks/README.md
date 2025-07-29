# Deep Learning Benchmarks and Datasets for Social Media Image Classification for Disaster Response

## Data
The crisis image benchmark dataset consists data from several data sources such as CrisisMMD, data from AIDR and Damage Multimodal Dataset (DMD). The purpose of this work was to develop a consolidated dataset, create non-overlapping train/dev/test set and provide a benchmark results for the community. More details can be found in [1].

## Data format and directories
### Directories
- **data/**: This is the main directory under which the following directory contains
  - **aidr_disaster_types/**: Contains images collected using AIDR system for disaster types task. In the paper it is referred as AIDR-DT.
  - **aidr_info/**: Contains images collected using AIDR system for informativeness task. In the paper it is referred as AIDR-Info.
  - **ASONAM17_Damage_Image_Dataset/**: In the paper this is referred as Damage Assessment Dataset (DAD)
  - **crisismmd/**: CrisisMMD dataset.
  - **multimodal-deep-learning-disaster-response-mouzannar/**: In the paper this is referred as Damage Multimodal Dataset (DMD)
- **tasks/**: Contains sub-directories for different tasks and associated data sources. In each sub-directory, the tsv file contains with label and image path.

### Format of the TSV file
* source: Name of the event or data source.
* image_id: Corresponds to the either tweet id from Twitter or id from the respective source.
* image_path: Relative path of the image.
* class_label: Corresponds to the class label. Depending on the task, the class label is different.


## Disaster response tasks
* Disaster types
  * Earthquake
  * Fire
  * Flood
  * Hurricane
  * Landslide
  * Not disaster
  * Other disaster
* Informativeness
   * Informative
   * Not informative
* Humanitarian categories
   * Affected, injured, or dead people
   * Infrastructure and utility damage
   * Not humanitarian
   * Rescue volunteering or donation effort
* Damage severity assessment
   * Severe damage
   * Mild damage
   * Little or no damage


## Citation
1. Firoj Alam, Ferda Ofli, Muhammad Imran, Tanvirul Alam, Umair Qazi, Deep Learning Benchmarks and Datasets for Social Media Image Classification for Disaster Response, In 2020 IEEE/ACM International Conference on Advances in Social Networks Analysis and Mining (ASONAM), 2020.
2. Firoj Alam, Ferda Ofli, and Muhammad Imran, CrisisMMD: Multimodal Twitter Datasets from Natural Disasters. In Proceedings of the 12th International AAAI Conference on Web and Social Media (ICWSM), 2018, Stanford, California, USA. [Bibtex]
3. Hussein Mozannar, Yara Rizk, and Mariette Awad, Damage Identification in Social Media Posts using Multimodal Deep Learning, In Proc. of ISCRAM, May 2018, pp. 529–543.

## Terms of Use
https://crisisnlp.qcri.org/terms-of-use.html
